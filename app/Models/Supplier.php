<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


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
        if (isAdmin() || isSuperAdmin() || isDyrektorM()) {

        } else {
            $user = Auth::user();
            $laboratoriesIds = $user->laboratory->pluck('id')->toArray();
//            dd($laboratories);
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

    public static function getSupplierPointsYear($supplierId)
    {
        $poolsId = \DB::table('pools_suppliers')->where('supplier_id', $supplierId)->distinct('pool_id')->pluck('pool_id')->toArray();
        $data = [];
        $supplier = self::where('id', $supplierId)->first();
        $poolsData = [];
        if (!empty($poolsId)) {
            // get pool data
            foreach ($poolsId as $poolId) {
                $pool = Pool::where('id', $poolId)->first();
                $singleResult = SupplierPoolQuestion::getResultForSinglePoolYear($poolId, $supplier);
                $poolsData[$poolId] = $singleResult;
            }
            if (!empty($poolsData)) {
                foreach ($poolsId as $poolId) {
                    if (isset($poolsData[$poolId])) {
                        $data[$poolId]['maxPoints'] = $poolsData[$poolId]['poolsMax'] ?? 0;

                        // foreach by results
                        if (isset($poolsData[$poolId]['resultsSummary'][$poolId]) && !empty($poolsData[$poolId]['resultsSummary'][$poolId])) {
                            foreach ($poolsData[$poolId]['resultsSummary'][$poolId] as $year=>$results) {
                                $count = count($results);
                                $sum = 0;
                                foreach ($results as $userId => $res) {
                                    $sum = $sum + $res;
                                }
                                $av = $sum / $count;
                                $av = sprintf("%.2f", $av);
                                $data[$poolId][$year] = $av;
                            }
                        }
                    }
                }
            }
        }
        $tmpData = $data;
        $data = [];
        if (!empty($tmpData)) {
            $allSumYear = 0;
            $allPointsYear = [];
            foreach ($tmpData as $poolId => $results) {
                if (isset($results['maxPoints'][$poolId]) && !empty($results['maxPoints'][$poolId])) {
                    $allSumYear = $allSumYear + $results['maxPoints'][$poolId];
                }
                unset($results['maxPoints']);
                foreach ($results as $year=>$points) {
                    if (!isset($allPointsYear[$year])) {
                        $allPointsYear[$year] = 0;
                    }
                    $allPointsYear[$year] = $allPointsYear[$year] + $points;
                }
            }
        }
        $data['maxPoints']=$allSumYear;
        $data['points'] = $allPointsYear;

        return $data;
    }
}
