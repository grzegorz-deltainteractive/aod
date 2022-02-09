<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Supplier extends Model
{
    public $table = 'suppliers';

    public static $statuses = [
        0 => 'Nieaktywny',
        1 => 'Aktywny'
    ];

    public static function getStatuses() {
        return self::$statuses;
    }

    public function departments() {
//        return $this->hasOne(Department::class, 'id', 'department' );
        return $this->belongsToMany(Department::class, 'suppliers_departments', 'supplier_id', 'department_id');
    }

    public function laboratories() {
//        return $this->hasOne(Laboratory::class, 'id', 'laboratory');
        return $this->belongsToMany(Laboratory::class, 'suppliers_laboratories', 'supplier_id', 'laboratory_id');
    }

    public static function getStatusName($status) {
        return self::$statuses[$status];
    }

    public function contacts()
    {
        return $this->hasMany(SuppliersContacts::class, 'supplier_id', 'id');
    }

    public static function getSupplierDepartmentsAndLaboratories($id)
    {
        $supplier = self::where('id', $id)->first();
        $deparmentsIds = [];
        $laboratoriesIds = [];
        if ($supplier) {
            $deparments = $supplier->departments;
            $laboratories = $supplier->laboratories;

            foreach ($deparments as $single) {
                $deparmentsIds[] = $single->id;
            }
            foreach ($laboratories as $single) {
                $laboratoriesIds[] = $single->id;
            }
        }
        return [
            'departmentsIds' => $deparmentsIds,
            'laboratoriesIds' => $laboratoriesIds
        ];
    }

    /**
     * get suppliers pools
     * @param $supplier Supplier model of supplier
     * @return void
     */
    public static function pools($supplier)
    {
        $return = [];
        $deparmentsAndLaboratoriesIds = self::getSupplierDepartmentsAndLaboratories($supplier->id);
//        dd($deparmentsAndLaboratoriesIds);
        $pools  = Pool::getPoolsForDepartmentAndLaboratoryList($deparmentsAndLaboratoriesIds['departmentsIds'], $deparmentsAndLaboratoriesIds['laboratoriesIds']);
//        $pools = Pool::where('department_id', $supplier->department)->where('laboratory_id', $supplier->laboratory)->get();

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
