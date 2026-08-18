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
        // 1. Stock Movements
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('product_variant_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('type'); // stock_in, stock_out, adjustment, transfer_in, transfer_out, damaged, expired, purchase, sale, return
                $table->integer('quantity');
                $table->integer('before_qty')->default(0);
                $table->integer('after_qty')->default(0);
                $table->string('reason')->nullable();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'branch_id']);
                $table->index('type');
            });
        }

        // 2. Stock Transfers
        if (!Schema::hasTable('stock_transfers')) {
            Schema::create('stock_transfers', function (Blueprint $table) {
                $table->id();
                $table->string('transfer_number')->unique();
                $table->unsignedBigInteger('from_branch_id');
                $table->unsignedBigInteger('to_branch_id');
                $table->string('status')->default('pending'); // pending, in_transit, completed, cancelled
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();

                $table->index(['from_branch_id', 'to_branch_id']);
            });
        }

        // 3. Stock Transfer Items
        if (!Schema::hasTable('stock_transfer_items')) {
            Schema::create('stock_transfer_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_transfer_id');
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('product_variant_id')->nullable();
                $table->integer('quantity');
                $table->integer('received_quantity')->default(0);
                $table->timestamps();

                $table->foreign('stock_transfer_id')->references('id')->on('stock_transfers')->onDelete('cascade');
            });
        }

        // 4. Purchase Order Items
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_order_id');
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('product_variant_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->integer('received_quantity')->default(0);
                $table->decimal('unit_cost', 12, 2)->default(0.00);
                $table->decimal('subtotal', 12, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            });
        }

        // 5. Expense Categories
        if (!Schema::hasTable('expense_categories')) {
            Schema::create('expense_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 6. Expenses
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('expense_category_id');
                $table->string('title');
                $table->decimal('amount', 12, 2);
                $table->date('expense_date');
                $table->string('payment_method')->default('cash');
                $table->string('reference_no')->nullable();
                $table->text('notes')->nullable();
                $table->string('attachment')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();

                $table->index(['branch_id', 'expense_date']);
                $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            });
        }

        // 7. Loyalty Transactions
        if (!Schema::hasTable('loyalty_transactions')) {
            Schema::create('loyalty_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->integer('points'); // positive for earned, negative for redeemed
                $table->string('type'); // earned, redeemed, expired, adjusted
                $table->unsignedBigInteger('order_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['customer_id', 'created_at']);
            });
        }

        // 8. Workflow Rules
        if (!Schema::hasTable('workflow_rules')) {
            Schema::create('workflow_rules', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('trigger_event'); // order_created, order_paid, stock_low, customer_vip, purchase_received
                $table->json('conditions')->nullable();
                $table->json('actions')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 9. Imported Products (Staging for URL / File Importer)
        if (!Schema::hasTable('imported_products')) {
            Schema::create('imported_products', function (Blueprint $table) {
                $table->id();
                $table->string('source_type')->default('file'); // file, url
                $table->string('source_url', 1000)->nullable();
                $table->json('data');
                $table->string('status')->default('draft'); // draft, reviewed, published, discarded
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // 10. Add Barcode, Attributes, Min/Max stock, Brand to products table if missing
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'min_stock')) {
                $table->integer('min_stock')->default(5)->after('qty');
            }
            if (!Schema::hasColumn('products', 'max_stock')) {
                $table->integer('max_stock')->default(100)->after('min_stock');
            }
            if (!Schema::hasColumn('products', 'attributes')) {
                $table->json('attributes')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
        });

        // 11. Add Barcode, Attributes to product_variants table if missing
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('product_variants', 'sale_price')) {
                $table->decimal('sale_price', 12, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('product_variants', 'weight')) {
                $table->string('weight')->nullable()->after('qty');
            }
            if (!Schema::hasColumn('product_variants', 'image')) {
                $table->string('image')->nullable()->after('weight');
            }
            if (!Schema::hasColumn('product_variants', 'status')) {
                $table->string('status')->default('active')->after('image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_products');
        Schema::dropIfExists('workflow_rules');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_movements');
    }
};
