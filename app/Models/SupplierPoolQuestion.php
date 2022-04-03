<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


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
        $user = Auth::user();
        $check = self::where('supplier_id', $supplier_id)->where('pool_id', $pool_id)->where('user_id', $user->id)->get();
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
        try {
            if (!empty($data)) {
                foreach ($data as $s) {
                    if ($s->supplier_id == $supplierId && $s->pool_id == $poolId && $s->user_id == $userId && $s->category_param_id == $parameterId) {
                        return $s->value;
                    }
                }
            }
        } catch (\Exception $ex) {

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
    public static function getSingleNotice($parameterId, $supplierId, $poolId, $userId)
    {
        $check = self::where('category_param_id', $parameterId)->where('supplier_id', $supplierId)->where('pool_id', $poolId)
            ->where('user_id', $userId)->pluck('notices');
        if ($check && !empty($check) && isset($check[0])) {
            return $check[0];
        } else {
            return '-';
        }
    }

    public static function calculatePoolResult($poolId, $supplier)
    {
        $pool = Pool::where('id', $poolId)->first();
        foreach ($pool->categories as $category) {
            $categoriesParameter = $category->categoriesParameters;
//            dd($categoriesParameter);
        }
    }

    public static function getResults ($pools, $supplier) {
        $results = [];
        $resultsSummary = [];
        $poolsMax = [];
        $poolsSummary = [];
        $poolsCount = [];
        if (!empty($pools)) {
            foreach ($pools as $year => $poolsList) {
                foreach ($poolsList as $pool ) {
                    $categories = $pool->categories;
                    foreach ($categories as $category) {
                        $categoriesParameter = $category->categoriesParameters;
                        foreach ($categoriesParameter as $single) {
                            if (!isset($poolsMax[$pool->id][$category->id])) {
                                $poolsMax[$pool->id][$category->id] = 0;
                            }
                            $poolsMax[$pool->id][$category->id] = $poolsMax[$pool->id][$category->id] + $single->rating_max;

                            $score = self::where('pool_id', $pool->id)->where('category_id', $category->id)
                                ->where('category_param_id', $single->id)->where('supplier_id', $supplier->id)->get();
                            if (!empty($score)) {
                                foreach ($score as $singleScore) {
                                    if (!isset($poolsCount[$pool->id][$singleScore->user_id])) {
                                        $poolsCount[$pool->id][$singleScore->user_id] = 1;
                                    }
                                    $results[$pool->id][$category->id][$single->id][$singleScore->user_id] = $singleScore->value;
                                    if (!isset($resultsSummary[$pool->id][$category->id][$single->id])) {
                                        $resultsSummary[$pool->id][$category->id][$single->id] = $singleScore->value;
                                    } else {
                                        $resultsSummary[$pool->id][$category->id][$single->id] = $resultsSummary[$pool->id][$category->id][$single->id] + $singleScore->value;
                                    }
                                    if (!isset($poolsSummary[$pool->id]['total'])) {
                                        $poolsSummary[$pool->id]['max'] = $single->rating_max;
                                        $poolsSummary[$pool->id]['total'] = $singleScore->value;
                                    } else {
                                        $poolsSummary[$pool->id]['max'] = $poolsSummary[$pool->id]['max'] + $single->rating_max;
                                        $poolsSummary[$pool->id]['total'] = $poolsSummary[$pool->id]['total'] + $singleScore->value;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return [
            'results' => $results,
            'resultsSummary' => $resultsSummary,
            'poolsMax' => $poolsMax,
            'poolsSummary' => $poolsSummary,
            'poolsCount' => $poolsCount
        ];
    }

    public static function getResultForSinglePool ($poolId, $supplier)
    {
        $results = [];
        $resultsSummary = [];
        $resultsSummaryParam = [];
        $poolsMax = [];
        $poolsSummary = [];
        $poolsCount = [];
        $pool = Pool::where('id', $poolId)->first();
        $users = [];

        $categories = $pool->categories;
        foreach ($categories as $category) {
            $categoriesParameter = $category->categoriesParameters;
            foreach ($categoriesParameter as $single) {
                if (!isset($poolsMax[$pool->id][$category->id])) {
                    $poolsMax[$pool->id][$category->id] = 0;
                } else {
                    $poolsMax[$pool->id][$category->id] = $poolsMax[$pool->id][$category->id] + $single->rating_max;
                }

                $score = self::where('pool_id', $pool->id)->where('category_id', $category->id)
                    ->where('category_param_id', $single->id)->where('supplier_id', $supplier->id)->get();
                if (!empty($score)) {
                    foreach ($score as $singleScore) {
                        if (!isset($resultsSummaryParam[$pool->id][$category->id][$single->id])) {
                            $resultsSummaryParam[$pool->id][$category->id][$single->id] = $single->rating_max;
                        } else {
                            $resultsSummaryParam[$pool->id][$category->id][$single->id] = $resultsSummaryParam[$pool->id][$category->id][$single->id] + $single->rating_max;
                        }
                        if (!isset($poolsCount[$pool->id][$singleScore->user_id])) {
                            $poolsCount[$pool->id][$singleScore->user_id] = 1;
                        }
                        $results[$pool->id][$category->id][$single->id][$singleScore->user_id] = $singleScore->value;
                        if (!isset($resultsSummary[$pool->id][$category->id][$single->id])) {
                            $resultsSummary[$pool->id][$category->id][$single->id] = $singleScore->value;
                        } else {
                            $resultsSummary[$pool->id][$category->id][$single->id] = $resultsSummary[$pool->id][$category->id][$single->id] + $singleScore->value;
                        }
                        if (!isset($poolsSummary[$pool->id]['total'])) {
                            $poolsSummary[$pool->id]['max'] = $single->rating_max;
                            $poolsSummary[$pool->id]['total'] = $singleScore->value;
                        } else {
                            $poolsSummary[$pool->id]['max'] = $poolsSummary[$pool->id]['max'] + $single->rating_max;
                            $poolsSummary[$pool->id]['total'] = $poolsSummary[$pool->id]['total'] + $singleScore->value;
                        }
                        if (!in_array($singleScore->user_id, $users)) {
                            $users[] = $singleScore->user_id;
                        }
                    }
                }
            }
        }
        return [
            'results' => $results,
            'resultsSummary' => $resultsSummary,
            'resultsSummaryParam' => $resultsSummaryParam,
            'poolsMax' => $poolsMax,
            'poolsSummary' => $poolsSummary,
            'poolsCount' => $poolsCount,
            'users' => $users
        ];
    }

    public static function getFilledData($poolId, $id)
    {
        $filled = self::where('pool_id', $poolId)->where('supplier_id', $id)->select(['user_id', 'created_at', 'pool_id', 'supplier_id'])->groupBy(['user_id', 'created_at', 'pool_id', 'supplier_id'])->distinct()->first();

        return $filled;
    }

    public static function getUserPools($userId)
    {
        $filled = self::where('user_id', $userId)->select(['user_id', 'created_at', 'pool_id', 'supplier_id'])->groupBy(['user_id', 'created_at', 'pool_id', 'supplier_id'])->distinct()->get();

        return $filled;
    }
}
