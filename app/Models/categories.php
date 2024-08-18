<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class categories extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    //one-to-many relationship DB
    public function products(): HasMany{
        //one-to-many relation (product with categories)
        return $this->hasMany(products::class);
    }

}
