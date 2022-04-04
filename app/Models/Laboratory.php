<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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

    /**
     * zwrot skrótu dla laboratorium
     * @param $id
     * @return string
     */
    public static function getLaboratoryShortcode($id)
    {
        $laboratory = self::where('id', $id)->first();
        if ($laboratory) {
            if (!empty($laboratory->skrot)) {
                return mb_strtoupper($laboratory->skrot);
            }
            return mb_strtoupper(substr($laboratory->name, 0, 6)).$laboratory->id;
        }
        return '';
    }

    /**
     * return users list by laboratory id
     * @param $id
     * @return array
     */
    public static function getLaboratoryUsers($id)
    {
        $users = [];
        $data = DB::table('user_laboratories')->where('laboratory_id', $id)->pluck('user_id')->toArray();
        if ($data) {
            $users = User::whereIn('id', $data)->pluck('name', 'id')->toArray();
        }

        return $users;
    }
}
