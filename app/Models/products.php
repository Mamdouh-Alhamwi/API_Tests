<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class products extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'categories_id']; 

    // many-to-many relationships DB
    public function orders() : BelongsToMany
      {
        //many-to-many relation (products with orders)
        return $this->belongsToMany(orders::class, 'product_order');
        //->using(Products_Orders::class);

        /*if the table withn't convention with the table, we write:
          return $this->belongsToMany(orders::class, 'orders_products', 'product_id','order_id);
        */
        
      }

    //one-to-many relationships DB
    public function categories(): BelongsTo{
      //one-to-many relation (products with categories)
      return $this->belongsTo(categories::class); 
    }

}

