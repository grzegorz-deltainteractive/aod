<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Pool extends Model
{
    public function categories()
    {
        return $this->hasMany(Categories::class, 'pool_id');
    }

    public static $statuses = [
        '1' => 'Robocza',
        '2' => 'Aktywna',
        '3' => 'Nieaktywna'
    ];

    public static function getStatuses()
    {
        return self::$statuses;
    }

    public static function getPoolsForDepartmentAndLaboratoryList($departmentsIds, $laboratoryIds)
    {
        $poolsForDepartments = DB::table('pools_departments')->whereIn('department_id', $departmentsIds)->pluck('pool_id')->toArray();
        $poolsForLaboratory = DB::table('pools_laboratories')->whereIn('laboratory_id', $laboratoryIds)->pluck('pool_id')->toArray();

        return self::whereIn('id', array_intersect($poolsForLaboratory, $poolsForDepartments))->get();
    }

}
