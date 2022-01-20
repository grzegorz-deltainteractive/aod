<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 03/01/2022
 * Time: 22:29
 */

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\CategoriesParameters;
use App\Models\Supplier;
use App\Models\SupplierPoolQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Models\Pool;

class SuppliersPoolsController extends VoyagerBaseController {

    public function pools($id)
    {
        $supplier = Supplier::where('id', $id)->first();
        $pools = [];
        if ($supplier) {
            $pools = Pool::where('department_id', $supplier->department)->where('laboratory_id', $supplier->laboratory)->get();
        }

        return view("suppliers/pools", ['pools' => $pools, 'supplier_id' => $id]);
    }

    /**
     * display filled pools
     * @param $id int supplier id
     * @param $poolId int pool id
     * @return void
     */
    public function filledPools($id, $poolId) {

        $pools = SupplierPoolQuestion::where('pool_id', $poolId)->where('supplier_id', $id)->select(['user_id', 'created_at', 'pool_id', 'supplier_id'])->groupBy(['user_id', 'created_at', 'pool_id', 'supplier_id'])->distinct()->get();
//        dd($pools);
        return view("suppliers/filled", ['pools' => $pools, 'supplier_id' => $id, 'pool_id' => $poolId]);
    }

    /**
     * show single filled data
     * @param $id
     * @param $poolId
     * @param $userId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filledPoolsSingle($id, $poolId, $userId)
    {
        $spq = SupplierPoolQuestion::where('supplier_id', $id)->where('pool_id', $poolId)->where('user_id', $userId)->get();
        $pool = Pool::where('id', $poolId)->first();
        return view("suppliers/filledSingle", ['pool' => $pool, 'data' => $spq, 'supplier_id' => $id, 'pool_id' => $poolId, 'user_id' => $userId]);
    }

    /**
     * fill pool
     * @param $id
     * @param $poolId
     * @return void
     */
    public function fillPool($id, $poolId)
    {

        $message = '';
        $data = request()->all();
        if (!empty($data)) {
            $user = Auth::user();
            $userId = $user->id;
            $check = SupplierPoolQuestion::where('user_id', $user)->where('pool_id', $poolId)->where('supplier_id', $id)->first();
            if ($check) {
                $message = 'Ten używkownik wypełnił już tą ankietę dla tego dostawcy';
            } else {
                // użytkownik jeszcze ankiety nie wypełniał więc pojedziemy
                if (isset($data['parameter']) && !empty($data['parameter'])) {
                    foreach ($data['parameter'] as $categoryId => $params) {
                        foreach ($params as $paramId => $value) {
                            $spq = new SupplierPoolQuestion();
                            $spq->user_id = $userId;
                            $spq->created_at = date('Y-m-d H:i:s');
                            $spq->updated_at = date('Y-m-d H:i:s');
                            $spq->pool_id = $poolId;
                            $spq->supplier_id = $id;
                            $spq->category_id = $categoryId;
                            $spq->category_param_id = $paramId;
                            $spq->value = $value;
                            if (isset($data['parameter-notices'][$categoryId][$paramId]) && !empty($data['parameter-notices'][$categoryId][$paramId])) {
                                $spq->notices = $data['parameter-notices'][$categoryId][$paramId];
                            }
                            $spq->save();
                        }
                    }
                    return redirect(route('suppliers.pools', ['id' => $id]));
                }
            }
        }
        $pool = Pool::where('id', $poolId)->first();
        return view('suppliers/fillPool', ['supplier_id' => $id, 'pool_id' => $poolId, 'pool' => $pool, 'message' => $message]);
    }
    public function categoriesSave($id)
    {
        if (!empty(request()->all())) {
            $data = request()->all();
            if (!empty($data)) {
                try {
                    $categories = Categories::where('pool_id', $id)->get();
                    foreach ($categories as $cat) {
                        CategoriesParameters::where('category_id', $cat->id)->delete();
                        $cat->delete();
                    }
                    foreach ($data as $single) {
                        $category = new Categories();
                        if (isset($single['id']) && !empty($single['id'])) {
                            $category->id = $single['id'];
                        }
                        $category->name = $single['name'];
                        $category->pool_id = $id;
                        if ($category->save()) {
                            if (isset($single['parameters']) && !empty($single['parameters'])) {
                                foreach ($single['parameters'] as $sParam) {
                                    $param = new CategoriesParameters();
                                    $param->category_id = $category->id;
                                    $param->name = $sParam['name'];
                                    $param->rating_min = $sParam['rating_min'] ?? 0;
                                    $param->rating_max = $sParam['rating_max'] ?? 20;
                                    $param->visible_for_lab = $sParam['visible_for_lab'] ?? 0;
                                    $param->save();
                                }
                            }
                        }
                    }
                } catch (\Exception $ex) {
                    \Log::debug($ex);
                }
            }
        }
    }
}
