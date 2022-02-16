<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Laboratory extends Model
{

    public static function getLaboratoriesById($ids)
    {
        $list = self::whereIn('id', $ids)->pluck('name', 'id');
        return $list;
    }

    public static function getAllLaboratoriesList()
    {
        $list = self::pluck('name', 'id');
        return $list;
    }

    public function suppliers() {
        return $this->belongsToMany(Supplier::class, 'suppliers_laboratories', 'laboratory_id', 'supplier_id');
    }
}
