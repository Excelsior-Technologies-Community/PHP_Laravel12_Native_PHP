<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Suppliers Table
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('payment_terms')->default('Net 30');
            $table->integer('lead_time_days')->default(7);
            $table->decimal('reliability_score', 3, 1)->default(4.8);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Extend Products Table with SKU, Barcode, and Supplier ID
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('name');
            $table->string('barcode')->nullable()->after('sku');
            $table->foreignId('supplier_id')->nullable()->after('status')
                  ->constrained('suppliers')->nullOnDelete();
        });

        // 3. Stock Movements Table
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('type'); // stock_in, stock_out, damaged, audit_adjustment, return
            $table->integer('quantity_change');
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->string('reason')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('created_by')->default('Admin / Inventory Manager');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['sku', 'barcode', 'supplier_id']);
        });

        Schema::dropIfExists('suppliers');
    }
};
