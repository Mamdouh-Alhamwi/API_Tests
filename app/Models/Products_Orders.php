<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Products_Orders extends Pivot
{
  protected $fillable = [
    'order_id',
    'product_id',
    'product_quantity', 
];

    //use HasFactory;
    public function product()
    {
      return $this->belongsTo(products::class);
    }
  
    public function order()
    {   
      return $this->belongsTo(orders::class);
    }

}
