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

    public static function getResultForSinglePoolYear ($poolId, $supplier)
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
                if (!isset($poolsMax[$pool->id])) {
                    $poolsMax[$pool->id] = 0;
                } else {
                    $poolsMax[$pool->id] = $poolsMax[$pool->id] + $single->rating_max;
                }

                $score = self::where('pool_id', $pool->id)->where('category_id', $category->id)
                    ->where('category_param_id', $single->id)->where('supplier_id', $supplier->id)->get();
                if (!empty($score)) {
                    foreach ($score as $singleScore) {
                        $year = date('Y', strtotime($singleScore->created_at));

                        $results[$pool->id][$year][$category->id][$single->id][$singleScore->user_id] = $singleScore->value;
                        if (!isset($resultsSummary[$pool->id][$year][$singleScore->user_id])) {
                            $resultsSummary[$pool->id][$year][$singleScore->user_id] = $singleScore->value;
                        } else {
                            $resultsSummary[$pool->id][$year][$singleScore->user_id] = $resultsSummary[$pool->id][$year][$singleScore->user_id] + $singleScore->value;
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
    public static function getFilledDataAll($poolId, $id)
    {
        $filled = self::where('pool_id', $poolId)->where('supplier_id', $id)->select(['user_id', 'created_at', 'pool_id', 'supplier_id'])->groupBy(['user_id', 'created_at', 'pool_id', 'supplier_id'])->distinct()->get();

        return $filled;
    }

    public static function getUserPools($userId)
    {
        $filled = self::where('user_id', $userId)->select(['user_id', 'created_at', 'pool_id', 'supplier_id'])->groupBy(['user_id', 'created_at', 'pool_id', 'supplier_id'])->distinct()->get();

        return $filled;
    }


    /**
     * generate years for supplier id by pools results
     * @param $supplierId
     * @return array
     */
    public static function getYearsForSupplier($supplierId)
    {
        $results = self::where('supplier_id', $supplierId)->select('created_at', 'supplier_id')->groupBy(['created_at', 'supplier_id'])->distinct()->get();
        $years = [];
        if (!empty($results)) {
            foreach ($results as $single) {
                $year = (int)date('Y', strtotime($single->created_at));
                if (!in_array($year, $years)) {
                    $years[] = $year;
                }
            }
        }
        return $years;
    }


    /**
     * get Average pools results
     * @param $poolId
     * @param $categoryId
     * @param $categoryParamId
     * @param $supplierId
     * @param $year
     * @return string|void
     */
    public static function getAverageResult($poolId, $categoryId, $categoryParamId, $supplierId, $year, $maxValue = 0)
    {
        $startDate = $year.'-01-01 00:00:00';
        $endDate = $year.'-12-31 23:59:59';
        $data = self::where('pool_id', $poolId)->where('category_id', $categoryId)->where('category_param_id', $categoryParamId)->where('supplier_id', $supplierId)
            ->where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->get();
        if (empty($data) || count($data) == 0) {
            return '-';
        } else {
            $value = 0;
            $max = 0;
            foreach ($data as $single) {
                $value = $value + $single->value;
                $max = $max + $maxValue;
            }
            return sprintf('%.2f', ($value/$maxValue));
        }
    }
}
