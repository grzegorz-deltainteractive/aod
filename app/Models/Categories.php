<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Categories extends Model
{

    public $table = 'categories';

    public $fillable = [
        'id', 'name', 'pool_id'
    ];

    public function categoriesParameters()
    {
        return $this->hasMany(CategoriesParameters::class, 'category_id');
    }
}
