<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CategoriesParameters extends Model
{

    public $table = 'categories_parameters';

    public $fillable = [
        'id', 'name', 'rating_min', 'rating_max', 'visible_for_lab', 'category_id'
    ];

    public function category() {
        return $this->belongsTo(Categories::class, 'category_id');
    }
}
