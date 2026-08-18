<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Warehouses & Multi-Warehouse Stocks
        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('warehouse_stocks')) {
            Schema::create('warehouse_stocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->unsignedBigInteger('product_variant_id')->nullable();
                $table->integer('qty')->default(0);
                $table->integer('committed_qty')->default(0);
                $table->integer('reserved_qty')->default(0);
                $table->integer('min_stock')->default(5);
                $table->integer('max_stock')->default(100);
                $table->string('bin_location')->nullable();
                $table->timestamps();

                $table->unique(['warehouse_id', 'product_id', 'product_variant_id'], 'wh_prod_var_unique');
            });
        }

        // 2. Stock Reservations
        if (!Schema::hasTable('stock_reservations')) {
            Schema::create('stock_reservations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->unsignedBigInteger('product_variant_id')->nullable();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('session_id')->nullable();
                $table->integer('qty')->default(1);
                $table->string('status')->default('active'); // active, released, fulfilled
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // 3. Product Batches & Expiry Tracking
        if (!Schema::hasTable('product_batches')) {
            Schema::create('product_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('batch_number');
                $table->date('mfg_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->decimal('cost_price', 12, 2)->default(0);
                $table->integer('qty')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Stock Counts / Cycle Counting
        if (!Schema::hasTable('stock_counts')) {
            Schema::create('stock_counts', function (Blueprint $table) {
                $table->id();
                $table->string('count_number')->unique();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('type')->default('cycle'); // full, cycle, partial
                $table->string('status')->default('draft'); // draft, in_progress, completed, reconciled
                $table->text('notes')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('stock_count_items')) {
            Schema::create('stock_count_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_count_id')->constrained('stock_counts')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->integer('expected_qty')->default(0);
                $table->integer('counted_qty')->default(0);
                $table->integer('difference')->default(0);
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        // 5. Product Bundles
        if (!Schema::hasTable('product_bundles')) {
            Schema::create('product_bundles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bundle_product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('item_product_id')->constrained('products')->onDelete('cascade');
                $table->integer('qty')->default(1);
                $table->decimal('discount_rate', 5, 2)->default(0); // discount % on item
                $table->timestamps();
            });
        }

        // 6. B2B & Wholesale Commerce
        if (!Schema::hasTable('b2b_companies')) {
            Schema::create('b2b_companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('company_code')->unique();
                $table->string('tax_id')->nullable(); // GST / VAT / Tax ID
                $table->string('contact_email');
                $table->string('contact_phone')->nullable();
                $table->string('billing_address')->nullable();
                $table->decimal('credit_limit', 12, 2)->default(0);
                $table->decimal('current_balance', 12, 2)->default(0);
                $table->string('payment_terms')->default('net_30'); // prepaid, net_15, net_30, net_60
                $table->string('status')->default('active'); // pending, active, suspended
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('b2b_buyers')) {
            Schema::create('b2b_buyers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('b2b_company_id')->constrained('b2b_companies')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('role')->default('buyer'); // admin, buyer, approver
                $table->decimal('spending_limit', 12, 2)->nullable();
                $table->boolean('can_approve_orders')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('b2b_tier_prices')) {
            Schema::create('b2b_tier_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->unsignedBigInteger('b2b_company_id')->nullable();
                $table->integer('min_qty')->default(1);
                $table->decimal('unit_price', 12, 2);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('b2b_quotes')) {
            Schema::create('b2b_quotes', function (Blueprint $table) {
                $table->id();
                $table->string('quote_number')->unique();
                $table->foreignId('b2b_company_id')->constrained('b2b_companies')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->string('status')->default('draft'); // draft, submitted, approved, rejected, converted
                $table->date('valid_until')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('b2b_quote_items')) {
            Schema::create('b2b_quote_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('b2b_quote_id')->constrained('b2b_quotes')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->integer('qty')->default(1);
                $table->decimal('requested_price', 12, 2);
                $table->decimal('approved_price', 12, 2)->nullable();
                $table->decimal('subtotal', 12, 2);
                $table->timestamps();
            });
        }

        // 7. Advanced Fulfillment & Delivery Slots
        if (!Schema::hasTable('fulfillment_orders')) {
            Schema::create('fulfillment_orders', function (Blueprint $table) {
                $table->id();
                $table->string('fulfillment_number')->unique();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('status')->default('unfulfilled'); // unfulfilled, picking, packed, shipped, delivered, cancelled
                $table->string('shipping_carrier')->nullable();
                $table->string('tracking_number')->nullable();
                $table->string('tracking_url')->nullable();
                $table->timestamp('shipped_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('fulfillment_order_items')) {
            Schema::create('fulfillment_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fulfillment_order_id')->constrained('fulfillment_orders')->onDelete('cascade');
                $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
                $table->integer('qty')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('delivery_slots')) {
            Schema::create('delivery_slots', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // e.g. "Morning Slot (9 AM - 1 PM)"
                $table->time('start_time');
                $table->time('end_time');
                $table->integer('max_orders')->default(20);
                $table->json('days_available')->nullable(); // ["monday", "tuesday", ...]
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 8. Wishlist, Saved Carts, Gift Cards & Store Credit
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }

        if (!Schema::hasTable('saved_carts')) {
            Schema::create('saved_carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('name')->default('My Saved Cart');
                $table->json('cart_data');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gift_cards')) {
            Schema::create('gift_cards', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->decimal('initial_balance', 12, 2);
                $table->decimal('current_balance', 12, 2);
                $table->string('currency')->default('USD');
                $table->string('recipient_email')->nullable();
                $table->string('pin', 10)->nullable();
                $table->date('expiry_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_credits')) {
            Schema::create('store_credits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
                $table->decimal('balance', 12, 2)->default(0);
                $table->string('currency')->default('USD');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('store_credit_transactions')) {
            Schema::create('store_credit_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_credit_id')->constrained('store_credits')->onDelete('cascade');
                $table->decimal('amount', 12, 2);
                $table->string('type'); // credit, debit
                $table->string('reference_type')->nullable(); // refund, gift, manual, purchase
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 9. Financial POS Register Sessions & Cash Reconciliation
        if (!Schema::hasTable('pos_register_sessions')) {
            Schema::create('pos_register_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->decimal('opening_amount', 12, 2)->default(0);
                $table->decimal('closing_amount', 12, 2)->nullable();
                $table->decimal('expected_cash', 12, 2)->default(0);
                $table->decimal('cash_sales', 12, 2)->default(0);
                $table->decimal('card_sales', 12, 2)->default(0);
                $table->decimal('upi_sales', 12, 2)->default(0);
                $table->decimal('difference', 12, 2)->default(0);
                $table->string('status')->default('open'); // open, closed
                $table->text('notes')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->timestamps();
            });
        }

        // 10. Abandoned Carts Tracker & Recovery
        if (!Schema::hasTable('abandoned_carts')) {
            Schema::create('abandoned_carts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->json('cart_data');
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('recovery_token')->unique();
                $table->integer('recovery_emails_sent')->default(0);
                $table->timestamp('recovered_at')->nullable();
                $table->timestamps();
            });
        }

        // 11. Developer Outbound Webhook Subscriptions & Logs
        if (!Schema::hasTable('webhook_subscriptions')) {
            Schema::create('webhook_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('target_url');
                $table->string('secret')->nullable();
                $table->json('events'); // ["order.created", "order.shipped", ...]
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('webhook_logs')) {
            Schema::create('webhook_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('webhook_subscription_id')->constrained('webhook_subscriptions')->onDelete('cascade');
                $table->string('event');
                $table->json('payload');
                $table->integer('response_status')->nullable();
                $table->text('response_body')->nullable();
                $table->integer('attempts')->default(1);
                $table->string('status')->default('delivered'); // delivered, failed
                $table->timestamps();
            });
        }

        // 12. Backup Records & History
        if (!Schema::hasTable('backups')) {
            Schema::create('backups', function (Blueprint $table) {
                $table->id();
                $table->string('file_name');
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('type')->default('database'); // database, files, full
                $table->string('status')->default('completed'); // completed, failed
                $table->string('checksum')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // 13. Column Additions for Products & Orders
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_bundle')) {
                $table->boolean('is_bundle')->default(false);
            }
            if (!Schema::hasColumn('products', 'is_preorder')) {
                $table->boolean('is_preorder')->default(false);
            }
            if (!Schema::hasColumn('products', 'preorder_release_date')) {
                $table->date('preorder_release_date')->nullable();
            }
            if (!Schema::hasColumn('products', 'allow_backorders')) {
                $table->boolean('allow_backorders')->default(false);
            }
            if (!Schema::hasColumn('products', 'hsn_code')) {
                $table->string('hsn_code', 50)->nullable();
            }
            if (!Schema::hasColumn('products', 'gst_rate')) {
                $table->decimal('gst_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'cgst_rate')) {
                $table->decimal('cgst_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'sgst_rate')) {
                $table->decimal('sgst_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'igst_rate')) {
                $table->decimal('igst_rate', 5, 2)->default(0);
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_slot_id')) {
                $table->unsignedBigInteger('delivery_slot_id')->nullable();
            }
            if (!Schema::hasColumn('orders', 'is_pickup')) {
                $table->boolean('is_pickup')->default(false);
            }
            if (!Schema::hasColumn('orders', 'gift_card_code')) {
                $table->string('gift_card_code')->nullable();
            }
            if (!Schema::hasColumn('orders', 'gift_card_amount')) {
                $table->decimal('gift_card_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'store_credit_amount')) {
                $table->decimal('store_credit_amount', 12, 2)->default(0);
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'hsn_code')) {
                $table->string('hsn_code', 50)->nullable();
            }
            if (!Schema::hasColumn('order_items', 'gst_rate')) {
                $table->decimal('gst_rate', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('order_items', 'cgst_amount')) {
                $table->decimal('cgst_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('order_items', 'sgst_amount')) {
                $table->decimal('sgst_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('order_items', 'igst_amount')) {
                $table->decimal('igst_amount', 12, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        // Safe rollback
    }
};
