<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class orders extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'user_id',
      'user_name',
      'total_price',
      'quantity',
      'product_quantity',
  ];

    //one-to-many relationships DB
    public function user(): BelongsTo{
      //one-to-many relation (user with orders)
      return $this->belongsTo(User::class);
    }

    //many-to-many relationships DB
    public function products() : BelongsToMany
      {
        //many-to-many relation (products with orders)
        return $this->belongsToMany(products::class, 'product_order')
        ->withPivot('product_quantity', 'liked');
        //->using(Products_Orders::class);  
      }

      public function likedProducts()
    {
        return $this->belongsToMany(products::class)
            ->wherePivot('liked', 'product_quantity',true);
    }

}
