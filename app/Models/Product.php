<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * HasFactory  → Allows use of model factories (helpful for testing & seeding)
     * SoftDeletes → Enables soft delete feature (uses deleted_at column)
     */

    /**
     * The attributes that are mass assignable.
     * fillable protects the model from mass assignment vulnerabilities.
     * Only these fields can be filled using create() or update().
     */
    protected $fillable = [
        'name',          // Product name
        'description',   // Product details
        'price',         // Product price
        'created_by',    // User ID who created the product
        'updated_by',    // User ID who last updated the product
        'status',        // Product status (Active/Inactive)
    ];
}

