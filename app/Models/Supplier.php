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

    public function poolsRelation() {
//        return $this->hasOne(Laboratory::class, 'id', 'laboratory');
        return $this->belongsToMany(Pool::class, 'pools_suppliers', 'supplier_id', 'pool_id');
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
     * get suppliers list
     * @param $forUser id for checking user
     * @return mixed
     */
    public static function getList($forUser = [])
    {
        if (empty($forUser)) {
            $list = self::whereNull('deleted_at')->pluck('name', 'id')->toArray();
        } else {
            $list = self::whereNull('deleted_at')->whereIn('id', $forUser)->pluck('name', 'id')->toArray();
        }
        return $list;
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

    /**\get supplier shortocode
     * @param $id
     * @return string
     */
    public static function getSupplierShortcode($id)
    {
        $supplier = self::where('id', $id)->first();
        if ($supplier) {
            if (!empty($supplier->skrot)) {
                return mb_strtoupper($supplier->skrot);
            }
            return mb_strtoupper(substr($supplier->name, 0, 3)).$supplier->id;
        }
        return '';
    }
}
