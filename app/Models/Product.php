<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function components()
    {
        return $this->hasMany(ProductComponent::class);
    }
}
