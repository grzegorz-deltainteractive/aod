<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Supplier extends Model
{
    public $table = 'suppliers';

    public static $statuses = [
        0 => 'nieaktywny',
        1 => 'aktywny'
    ];

    public function departmentRelation() {
        return $this->hasOne(Department::class, 'id', 'department' );
    }

    public function laboratoryRelation() {
        return $this->hasOne(Laboratory::class, 'id', 'laboratory');
    }

    public static function getStatusName($status) {
        return self::$statuses[$status];
    }

    /**
     * get suppliers pools
     * @param $supplier Supplier model of supplier
     * @return void
     */
    public static function pools($supplier)
    {
        $return = [];
        $pools = Pool::where('department_id', $supplier->department)->where('laboratory_id', $supplier->laboratory)->get();
        foreach ($pools as $pool) {
            $year = date('Y', strtotime($pool->data_wydania_ankiety));
            if (!array_key_exists($year, $return)) {
                $return[$year] = [];
            }
            $return[$year][] = $pool;
        }
        krsort($return);
        return $return;
    }
}
