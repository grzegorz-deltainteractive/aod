<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 02/02/2022
 * Time: 21:56
 */

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SupplierPoolStatus extends Model {
    public $table = 'suppliers_pools_status';
    public $fillable = ['pool_id', 'user_id', 'supplier_id', 'filled_date', 'accepted_date', 'accepted_user_id', 'admin_edited_date', 'admin_edited_user', 'dm_accepted_date', 'dm_accepted_user'];

    /**
     * save filled date
     * @param $user_id
     * @param $pool_id
     * @param $supplier_id
     * @return void
     */
    public static function addPoolFillDate($user_id, $pool_id, $supplier_id) {
        $model = new self();
        $model->pool_id = $pool_id;
        $model->user_id = $user_id;
        $model->supplier_id = $supplier_id;
        $model->filled_date = date('Y-m-d H:i:s');
        $model->save();
    }

    public static function getStatus($user_id, $pool_id, $supplier_id)
    {
        $check = self::where('pool_id', $pool_id)->where('user_id', $user_id)->where('supplier_id', $supplier_id)->first();
        return $check;
    }

    /**
     * get DM status
     * @param $user_id
     * @param $pool_id
     * @param $supplier_id
     * @return mixed
     */
    public static function getStatusDm($user_id, $pool_id, $supplier_id)
    {
        $check = self::where('pool_id', $pool_id)->where('user_id', $user_id)->where('supplier_id', $supplier_id)->whereNotNull('dm_accepted_date')->first();
        return $check;
    }

    /**
     * return accepted status
     * @param $user_id
     * @param $pool_id
     * @param $supplier_id
     * @return string
     */
    public static function getPoolFilledStatus($user_id, $pool_id, $supplier_id) {
        $check = self::where('pool_id', $pool_id)->where('user_id', $user_id)->where('supplier_id', $supplier_id)->first();
        if (!$check || empty($check) ) {
            return 'unfilled';
        } else {
            if (empty($check->accepted_date)) {
                return 'unaceppted';
            } else {
                $userName = User::where('id', $check->accepted_user_id)->first();
                $userName = $userName->name;
                $statusString = 'Zaakceptowano dnia '.$check->accepted_date .' przez '.$userName;
                return $statusString;
            }
        }
    }

    /**
     * return date supplied year
     * @param $pool_id
     * @param $supplier_id
     * @return false|string
     */
    public static function getPoolFilledYear($pool_id, $supplier_id)
    {
        $check = self::where('pool_id', $pool_id)->where('supplier_id', $supplier_id)->first();
        if (!empty($check)) {
            return date('Y', strtotime($check->filled_date));
        } else {
            return '-';
        }
    }

    public static function getPoolFilledUserYear($pool_id, $supplier_id, $userId)
    {
        $check = self::where('pool_id', $pool_id)->where('supplier_id', $supplier_id)->where('user_id', $userId)->first();
        if (!empty($check)) {
            return date('Y', strtotime($check->filled_date));
        } else {
            return '-';
        }
    }

    /**
     * Accept pool
     * @param $user_id
     * @param $pool_id
     * @param $supplier_id
     * @return bool
     */
    public static function acceptPool($user_id, $pool_id, $supplier_id) {
        $check = self::where('pool_id', $pool_id)->where('user_id', $user_id)->where('supplier_id', $supplier_id)->first();
        if ($check) {
            $check->accepted_date = date('Y-m-d H:i:s');
            $check->accepted_user_id = Auth::user()->id;
            if ($check->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * accept pool by dyrektor medyczny
     * @param $user_id
     * @param $pool_id
     * @param $supplier_id
     * @return bool
     */
    public static function acceptDMPool($user_id, $pool_id, $supplier_id) {
        $check = self::where('pool_id', $pool_id)->where('user_id', $user_id)->where('supplier_id', $supplier_id)->first();
//        dd($check);
        if ($check) {
            $check->dm_accepted_date = date('Y-m-d H:i:s');
            $check->dm_accepted_user_id = Auth::user()->id;
            if ($check->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * set admin edited pool status
     * @param $user_id
     * @param $pool_id
     * @param $supplier_id
     * @return bool
     */
    public static function addAdminEditDate($user_id, $pool_id, $supplier_id)
    {
        $check = self::where('pool_id', $pool_id)->where('user_id', $user_id)->where('supplier_id', $supplier_id)->first();
        if ($check) {
            $check->admin_edited_date = date('Y-m-d H:i:s');
            $check->admin_edited_user = Auth::user()->id;
            if ($check->save()) {
                return true;
            }
        }
        return false;
    }
}
