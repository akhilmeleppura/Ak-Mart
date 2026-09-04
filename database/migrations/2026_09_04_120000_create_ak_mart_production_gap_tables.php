<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Grocery-Specific Columns on Products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_weight_based')) {
                $table->boolean('is_weight_based')->default(false)->after('qty');
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit', 30)->default('piece')->after('is_weight_based'); // kg, g, L, ml, piece, dozen, pack, box, bottle, bundle
            }
            if (!Schema::hasColumn('products', 'quantity_step')) {
                $table->decimal('quantity_step', 8, 3)->default(1.000)->after('unit'); // e.g. 0.250 for 250g
            }
            if (!Schema::hasColumn('products', 'min_order_qty')) {
                $table->decimal('min_order_qty', 8, 3)->default(1.000)->after('quantity_step');
            }
            if (!Schema::hasColumn('products', 'max_order_qty')) {
                $table->decimal('max_order_qty', 8, 3)->nullable()->after('min_order_qty');
            }
            if (!Schema::hasColumn('products', 'unit_price_ratio')) {
                $table->decimal('unit_price_ratio', 10, 4)->nullable()->after('max_order_qty'); // price per base unit
            }
            if (!Schema::hasColumn('products', 'price_per_unit_label')) {
                $table->string('price_per_unit_label', 50)->nullable()->after('unit_price_ratio'); // e.g. "$4.50 / kg"
            }
            if (!Schema::hasColumn('products', 'is_perishable')) {
                $table->boolean('is_perishable')->default(false)->after('price_per_unit_label');
            }
            if (!Schema::hasColumn('products', 'expiry_shelf_life_days')) {
                $table->integer('expiry_shelf_life_days')->nullable()->after('is_perishable');
            }
            if (!Schema::hasColumn('products', 'allow_substitution')) {
                $table->boolean('allow_substitution')->default(true)->after('expiry_shelf_life_days');
            }
            if (!Schema::hasColumn('products', 'substitution_notes')) {
                $table->text('substitution_notes')->nullable()->after('allow_substitution');
            }
            if (!Schema::hasColumn('products', 'approval_status')) {
                $table->string('approval_status', 30)->default('published')->after('substitution_notes'); // draft, pending_review, published, unpublished, archived
            }
        });

        // 2. Batch / Lot Management Columns
        Schema::table('product_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('product_batches', 'supplier_reference')) {
                $table->string('supplier_reference')->nullable()->after('batch_number');
            }
            if (!Schema::hasColumn('product_batches', 'received_date')) {
                $table->date('received_date')->nullable()->after('mfg_date');
            }
            if (!Schema::hasColumn('product_batches', 'available_qty')) {
                $table->integer('available_qty')->default(0)->after('qty');
            }
            if (!Schema::hasColumn('product_batches', 'reserved_qty')) {
                $table->integer('reserved_qty')->default(0)->after('available_qty');
            }
            if (!Schema::hasColumn('product_batches', 'damaged_qty')) {
                $table->integer('damaged_qty')->default(0)->after('reserved_qty');
            }
            if (!Schema::hasColumn('product_batches', 'expired_qty')) {
                $table->integer('expired_qty')->default(0)->after('damaged_qty');
            }
            if (!Schema::hasColumn('product_batches', 'status')) {
                $table->string('status', 30)->default('active')->after('expired_qty'); // active, near_expiry, expired, depleted, quarantine
            }
        });

        // 3. Stock Reservations Extension
        Schema::table('stock_reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_reservations', 'product_batch_id')) {
                $table->unsignedBigInteger('product_batch_id')->nullable()->after('warehouse_id');
            }
            if (!Schema::hasColumn('stock_reservations', 'reserved_by_user_id')) {
                $table->unsignedBigInteger('reserved_by_user_id')->nullable()->after('session_id');
            }
            if (!Schema::hasColumn('stock_reservations', 'idempotency_key')) {
                $table->string('idempotency_key', 100)->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('stock_reservations', 'reason')) {
                $table->string('reason')->default('checkout')->after('idempotency_key');
            }
        });

        // 4. Order Management Columns
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'cancellation_reason_code')) {
                $table->string('cancellation_reason_code', 50)->nullable()->after('order_status');
            }
            if (!Schema::hasColumn('orders', 'cancellation_notes')) {
                $table->text('cancellation_notes')->nullable()->after('cancellation_reason_code');
            }
            if (!Schema::hasColumn('orders', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('cancellation_notes');
            }
            if (!Schema::hasColumn('orders', 'customer_notes')) {
                $table->text('customer_notes')->nullable()->after('internal_notes');
            }
            if (!Schema::hasColumn('orders', 'rescheduled_delivery_at')) {
                $table->timestamp('rescheduled_delivery_at')->nullable()->after('customer_notes');
            }
            if (!Schema::hasColumn('orders', 'credit_note_number')) {
                $table->string('credit_note_number', 50)->nullable()->after('rescheduled_delivery_at');
            }
            if (!Schema::hasColumn('orders', 'delivery_otp')) {
                $table->string('delivery_otp', 10)->nullable()->after('credit_note_number');
            }
            if (!Schema::hasColumn('orders', 'delivery_proof_photo_url')) {
                $table->string('delivery_proof_photo_url')->nullable()->after('delivery_otp');
            }
            if (!Schema::hasColumn('orders', 'delivery_signature_url')) {
                $table->string('delivery_signature_url')->nullable()->after('delivery_proof_photo_url');
            }
            if (!Schema::hasColumn('orders', 'delivery_failed_reason')) {
                $table->string('delivery_failed_reason')->nullable()->after('delivery_signature_url');
            }
            if (!Schema::hasColumn('orders', 'delivery_attempts')) {
                $table->integer('delivery_attempts')->default(0)->after('delivery_failed_reason');
            }
        });

        // 5. Order Items Extension (Decimal Quantities, Item-level status, Substitution)
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'item_status')) {
                $table->string('item_status', 30)->default('pending')->after('qty'); // pending, picking, picked, packed, cancelled, fulfilled, returned
            }
            if (!Schema::hasColumn('order_items', 'cancelled_qty')) {
                $table->decimal('cancelled_qty', 8, 3)->default(0)->after('item_status');
            }
            if (!Schema::hasColumn('order_items', 'cancellation_reason')) {
                $table->string('cancellation_reason')->nullable()->after('cancelled_qty');
            }
            if (!Schema::hasColumn('order_items', 'substitution_product_id')) {
                $table->unsignedBigInteger('substitution_product_id')->nullable()->after('cancellation_reason');
            }
            if (!Schema::hasColumn('order_items', 'substitution_status')) {
                $table->string('substitution_status', 30)->nullable()->after('substitution_product_id'); // none, proposed, accepted, rejected
            }
            if (!Schema::hasColumn('order_items', 'product_batch_id')) {
                $table->unsignedBigInteger('product_batch_id')->nullable()->after('substitution_status');
            }
        });

        // 6. Order Status Histories (Timeline)
        if (!Schema::hasTable('order_status_histories')) {
            Schema::create('order_status_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->string('from_status')->nullable();
                $table->string('to_status');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('actor_type')->default('staff'); // customer, staff, driver, system
                $table->string('reason')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        // 7. Warehouse Picking & Packing Extensions
        Schema::table('fulfillment_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('fulfillment_orders', 'picker_id')) {
                $table->foreignId('picker_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('fulfillment_orders', 'picking_started_at')) {
                $table->timestamp('picking_started_at')->nullable();
            }
            if (!Schema::hasColumn('fulfillment_orders', 'picking_completed_at')) {
                $table->timestamp('picking_completed_at')->nullable();
            }
            if (!Schema::hasColumn('fulfillment_orders', 'packer_id')) {
                $table->foreignId('packer_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('fulfillment_orders', 'packing_completed_at')) {
                $table->timestamp('packing_completed_at')->nullable();
            }
        });

        // 8. Fulfillment Packages (Multiple Packages per order with weights & barcodes)
        if (!Schema::hasTable('fulfillment_packages')) {
            Schema::create('fulfillment_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fulfillment_order_id')->constrained('fulfillment_orders')->onDelete('cascade');
                $table->string('package_barcode')->unique();
                $table->decimal('weight_kg', 8, 3)->default(0);
                $table->string('package_type')->default('carton'); // box, crate, bag, thermal_tote
                $table->foreignId('sealed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('verification_status')->default('verified');
                $table->timestamps();
            });
        }

        // 9. Delivery Zones & Dynamic Fees
        if (!Schema::hasTable('delivery_zones')) {
            Schema::create('delivery_zones', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('branch_id')->default(1);
                $table->decimal('min_order_amount', 12, 2)->default(0);
                $table->decimal('free_delivery_threshold', 12, 2)->default(50.00);
                $table->decimal('base_delivery_fee', 12, 2)->default(3.99);
                $table->decimal('per_km_fee', 8, 2)->default(0.50);
                $table->decimal('max_distance_km', 8, 2)->default(25.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 10. Return Requests & Reverse Logistics Extensions
        Schema::table('return_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('return_requests', 'rma_number')) {
                $table->string('rma_number', 50)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('return_requests', 'pickup_driver_id')) {
                $table->unsignedBigInteger('pickup_driver_id')->nullable()->after('branch_id');
            }
            if (!Schema::hasColumn('return_requests', 'pickup_scheduled_at')) {
                $table->timestamp('pickup_scheduled_at')->nullable()->after('pickup_driver_id');
            }
            if (!Schema::hasColumn('return_requests', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable()->after('pickup_scheduled_at');
            }
            if (!Schema::hasColumn('return_requests', 'inspected_by_user_id')) {
                $table->unsignedBigInteger('inspected_by_user_id')->nullable()->after('picked_up_at');
            }
            if (!Schema::hasColumn('return_requests', 'inspection_result')) {
                $table->string('inspection_result', 40)->nullable()->after('inspected_by_user_id'); // approved, rejected, damaged, restocked, return_to_vendor
            }
            if (!Schema::hasColumn('return_requests', 'refund_method')) {
                $table->string('refund_method', 30)->default('wallet')->after('inspection_result'); // wallet, original_payment
            }
            if (!Schema::hasColumn('return_requests', 'refund_transaction_id')) {
                $table->string('refund_transaction_id')->nullable()->after('refund_method');
            }
            if (!Schema::hasColumn('return_requests', 'credit_note_id')) {
                $table->unsignedBigInteger('credit_note_id')->nullable()->after('refund_transaction_id');
            }
        });

        // 11. Item-Level Return Line Items
        if (!Schema::hasTable('return_request_items')) {
            Schema::create('return_request_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('return_request_id')->constrained('return_requests')->onDelete('cascade');
                $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
                $table->decimal('qty', 8, 3)->default(1);
                $table->string('reason')->nullable();
                $table->string('condition')->default('unopened'); // unopened, opened, damaged, expired
                $table->decimal('refund_amount', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        // 12. Credit Notes Table
        if (!Schema::hasTable('credit_notes')) {
            Schema::create('credit_notes', function (Blueprint $table) {
                $table->id();
                $table->string('credit_note_number', 50)->unique();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->unsignedBigInteger('return_request_id')->nullable();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('status', 30)->default('issued'); // issued, applied, refunded, void
                $table->timestamps();
            });
        }

        // 13. Financial Payment Reconciliations & Ledger
        if (!Schema::hasTable('payment_reconciliations')) {
            Schema::create('payment_reconciliations', function (Blueprint $table) {
                $table->id();
                $table->string('gateway', 40); // stripe, razorpay, cod, virtual_upi
                $table->string('transaction_id', 100)->unique();
                $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
                $table->decimal('amount', 12, 2);
                $table->string('currency', 10)->default('USD');
                $table->decimal('gateway_fee', 10, 2)->default(0);
                $table->decimal('net_settlement', 12, 2)->default(0);
                $table->string('status', 40)->default('captured'); // authorized, captured, refunded, disputed, failed
                $table->boolean('signature_verified')->default(false);
                $table->string('idempotency_key', 120)->nullable()->unique();
                $table->json('raw_payload')->nullable();
                $table->timestamps();
            });
        }

        // 14. Customer CRM & Segmentation Columns
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'customer_segment')) {
                $table->string('customer_segment', 30)->default('NEW')->after('locale'); // NEW, ACTIVE, LOYAL, AT_RISK, INACTIVE, VIP
            }
            if (!Schema::hasColumn('users', 'rfm_score')) {
                $table->integer('rfm_score')->default(111)->after('customer_segment');
            }
            if (!Schema::hasColumn('users', 'lifetime_spend')) {
                $table->decimal('lifetime_spend', 12, 2)->default(0)->after('rfm_score');
            }
            if (!Schema::hasColumn('users', 'total_orders_count')) {
                $table->integer('total_orders_count')->default(0)->after('lifetime_spend');
            }
            if (!Schema::hasColumn('users', 'last_ordered_at')) {
                $table->timestamp('last_ordered_at')->nullable()->after('total_orders_count');
            }
            if (!Schema::hasColumn('users', 'marketing_consent')) {
                $table->boolean('marketing_consent')->default(true)->after('last_ordered_at');
            }
        });

        // 15. Customer Internal Notes Table
        if (!Schema::hasTable('customer_notes')) {
            Schema::create('customer_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('note');
                $table->boolean('is_pinned')->default(false);
                $table->timestamps();
            });
        }

        // 16. Product Slug History (Prevent broken SEO / 404s on product rename)
        if (!Schema::hasTable('product_slug_histories')) {
            Schema::create('product_slug_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('slug')->index();
                $table->timestamps();
            });
        }

        // 17. Loyalty Tiers Table
        if (!Schema::hasTable('loyalty_tiers')) {
            Schema::create('loyalty_tiers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50); // Bronze, Silver, Gold, Platinum
                $table->decimal('min_spend', 12, 2)->default(0);
                $table->decimal('points_multiplier', 5, 2)->default(1.00);
                $table->json('perks')->nullable();
                $table->timestamps();
            });
        }

        // 18. Audit Logs Table Comprehensive Columns
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'actor_id')) {
                $table->unsignedBigInteger('actor_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('audit_logs', 'actor_role')) {
                $table->string('actor_role', 50)->nullable()->after('actor_id');
            }
            if (!Schema::hasColumn('audit_logs', 'action')) {
                $table->string('action', 80)->default('update')->after('actor_role');
            }
            if (!Schema::hasColumn('audit_logs', 'entity_type')) {
                $table->string('entity_type', 80)->nullable()->after('action');
            }
            if (!Schema::hasColumn('audit_logs', 'entity_id')) {
                $table->unsignedBigInteger('entity_id')->nullable()->after('entity_type');
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('new_values');
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('audit_logs', 'request_id')) {
                $table->string('request_id', 100)->nullable()->after('user_agent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe rollback
    }
};
