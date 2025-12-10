<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This function executes when we run: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            // Primary Key (Auto Increment)
            $table->id();  
            // Creates 'id' column (BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY)

            // Product Name
            $table->string('name');
            // Stores product name using VARCHAR(255)

            // Product Description
            $table->text('description')->nullable();
            // 'text' allows long description, and nullable() makes it optional

            // Product Price
            $table->decimal('price', 10, 2);
            // decimal(10,2) means: max 10 digits total, 2 digits after the decimal

            // User who created the product
            $table->unsignedBigInteger('created_by')->nullable();
            // Stores user ID (foreign key in future) and can be null

            // User who last updated the product
            $table->unsignedBigInteger('updated_by')->nullable();
            // Same as above — stores user ID and can be null

            // Product Status
            $table->string('status')->default('Active');
            // Default value = 'Active' if no status provided

            // Timestamp Columns
            $table->timestamps();
            // Creates 'created_at' & 'updated_at' columns automatically

            // Soft Delete Column
            $table->softDeletes();
            // Creates 'deleted_at' column → used for soft delete
        });
    }

    /**
     * Reverse the migrations.
     * Runs when we use: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        // Drops the 'products' table only if it already exists
    }
};
