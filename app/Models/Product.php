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
        'category_id',
        'name',
        'description',
        'price',
        'created_by',
        'updated_by',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}