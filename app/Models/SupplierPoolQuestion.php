<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class SupplierPoolQuestion extends Model
{
    protected $table = 'suppliers_pools_questions';

    protected $fillable = [
        'id', 'pool_id', 'category_id', 'category_param_id', 'supplier_id', 'value', 'notices', 'user_id'
    ];

    // relations

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function checkPoolEntered($supplier_id, $pool_id)
    {
        $check = self::where('supplier_id', $supplier_id)->where('pool_id', $pool_id)->get();
        if ($check && count($check) > 0) {
            return true;
        }
        return false;
    }


    /**
     * @param $parameterId
     * @param $data Model dane z tabeli
     * @return void
     */
    public static function getValue($parameterId, $data, $supplierId, $poolId, $userId)
    {
        if (!empty($data)) {
            foreach ($data as $s) {
                if ($s->supplier_id == $supplierId && $s->pool_id == $poolId && $s->user_id == $userId && $s->category_param_id == $parameterId) {
                    if (!empty($s->value)) {
                        return $s->value;
                    }
                }
            }
        }
        return '-';
    }
    public static function getNotices($parameterId, $data, $supplierId, $poolId, $userId)
    {
        if (!empty($data)) {
            foreach ($data as $s) {
                if ($s->supplier_id == $supplierId && $s->pool_id == $poolId && $s->user_id == $userId && $s->category_param_id == $parameterId) {
                    if (!empty($s->notices)) {
                        return $s->notices;
                    }
                }
            }
        }
        return '-';
    }
}
