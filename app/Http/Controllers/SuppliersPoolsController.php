<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 03/01/2022
 * Time: 22:29
 */

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\CategoriesParameters;
use App\Models\PoolSupplier;
use App\Models\Supplier;
use App\Models\SupplierPoolQuestion;
use App\Models\SupplierPoolStatus;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Models\Pool;
use Barryvdh\DomPDF\Facade\Pdf;

class SuppliersPoolsController extends VoyagerBaseController {

    public function pools($id)
    {
        $supplier = Supplier::where('id', $id)->first();
        $pools = [];
        if ($supplier) {
            $deparments = $supplier->departments;
            $laboratories = $supplier->laboratories;
            $deparmentsIds = [];
            $laboratoriesIds = [];
            foreach ($deparments as $single) {
                $deparmentsIds[] = $single->id;
            }
            foreach ($laboratories as $single) {
                $laboratoriesIds[] = $single->id;
            }
//            $pools = Pool::getPoolsForDepartmentAndLaboratoryList($deparmentsIds, $laboratoriesIds);
            $supplierPools = PoolSupplier::where('supplier_id', $id)->pluck('pool_id')->toArray();
            $pools = Pool::whereIn('id', $supplierPools)->get();
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

        return view("suppliers/filled", ['pools' => $pools, 'supplier_id' => $id, 'pool_id' => $poolId]);
    }

    /**
     * accept single pool
     * @param $id
     * @param $poolId
     * @param $userId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function acceptPool($id, $poolId, $userId)
    {
        if (SupplierPoolStatus::acceptPool($userId, $poolId, $id)) {
            return redirect(route('suppliers.pools.filled', ['id' => $id, 'poolId' => $poolId]))->with([
                    'message'    => 'Poprawnie zaakceptowano ankietę',
                    'alert-type' => 'success',
                ]
            );
        } else {
            return redirect(route('suppliers.pools.filled', ['id' => $id, 'poolId' => $poolId]))->with([
                    'message'    => 'Nie udało się zaakceptować ankiety',
                    'alert-type' => 'error',
                ]
            );
        }
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

    public function filledPoolsSinglePdf($id, $poolId, $userId)
    {
        $spq = SupplierPoolQuestion::where('supplier_id', $id)->where('pool_id', $poolId)->where('user_id', $userId)->get();
        $pool = Pool::where('id', $poolId)->first();
        try {
            view()->share('supplier_id', $id);
            view()->share('user_id', $userId);
            view()->share('pool', $pool);
            view()->share('pool_id', $poolId);
            view()->share('data', $spq);
            $pdf = PDF::loadView('suppliers/filledSinglePdf');
//            $pdf->setWarnings(true);
            $pdf->setPaper('a4', 'landscape');
//            $pdf->save('ankieta-'.$poolId.'-'.$supplierId.'.pdf');
            return $pdf->stream('ankieta-pojedyncza-'.$poolId.'-'.$id.'.pdf');
        } catch (\Exception $ex) {
            dd($ex);
        }
//        return view("suppliers/filledSinglePdf", ['pool' => $pool, 'data' => $spq, 'supplier_id' => $id, 'pool_id' => $poolId, 'user_id' => $userId]);
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
                    SupplierPoolStatus::addPoolFillDate($userId, $poolId, $id);
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

    public function displaypools($poolId, $supplierId)
    {
        $supplier = Supplier::where('id', $supplierId)->first();
        $result = SupplierPoolQuestion::getResultForSinglePool($poolId, $supplier);
        $pool = Pool::where('id', $poolId)->first();
        $ut = [];
        foreach ($result['users'] as &$user) {
            $userRead = User::where('id', $user)->first();
            if (isset($userRead->laboratory[0]) && !empty($userRead->laboratory[0])) {
                $ut[$user] = $userRead->name .' - '.$userRead->laboratory[0]->name;
            } else {
                $ut[$user] = $userRead->name .' - nie wybrano laboratorium';
            }
        }
        unset($user);
//        dd($result);
        return view('suppliers/averagePools', ['supplier' => $supplier, 'results' => $result, 'pool' => $pool, 'ut' => $ut]);
    }

    /**
     * generate pdf
     * @param $poolId
     * @param $supplierId
     * @return \Illuminate\Http\Response|void
     */
    public function displaypoolsPdf($poolId, $supplierId)
    {
        $supplier = Supplier::where('id', $supplierId)->first();
        $result = SupplierPoolQuestion::getResultForSinglePool($poolId, $supplier);
        $pool = Pool::where('id', $poolId)->first();
        $ut = [];
        foreach ($result['users'] as &$user) {
            $userRead = User::where('id', $user)->first();
            if (isset($userRead->laboratory[0]) && !empty($userRead->laboratory[0])) {
                $ut[$user] = $userRead->name .' - '.$userRead->laboratory[0]->name;
            } else {
                $ut[$user] = $userRead->name .' - nie wybrano laboratorium';
            }
        }
        unset($user);
//        dd($result);
//        return view('suppliers/averagePoolsPdf', ['supplier' => $supplier, 'results' => $result, 'pool' => $pool, 'ut' => $ut]);
        try {
            view()->share('supplier', $supplier);
            view()->share('results', $result);
            view()->share('pool', $pool);
            view()->share('ut', $ut);
            $pdf = PDF::loadView('suppliers/averagePoolsPdf');

//            $pdf->setWarnings(true);
            $pdf->setPaper('a4', 'portrait');
            $path = public_path().'/pdf/';
//            $pdf->save($path.'ankieta-'.$poolId.'-'.$supplierId.'.pdf', 'UTF-8');
            return $pdf->stream('ankieta-'.$poolId.'-'.$supplierId.'-'.date('Y', strtotime($pool->data_wydania_ankiety)).'_'.$pool->numer_procedury.'_'.$supplier->skrot.'.pdf');
//            return response()->download($path.'ankieta-'.$poolId.'-'.$supplierId.'.pdf');
        } catch (\Exception $ex) {
            dd($ex);
        }

    }

    public function displayParameterDraw($poolId, $supplierId, $parameterId)
    {
        $supplier = Supplier::where('id', $supplierId)->first();
        $result = SupplierPoolQuestion::getResultForSinglePool($poolId, $supplier);
        $pool = Pool::where('id', $poolId)->first();
        $ut = [];
        foreach ($result['users'] as &$user) {
            $userRead = User::where('id', $user)->first();
            if (isset($userRead->laboratory[0]) && !empty($userRead->laboratory[0])) {
                $ut[$user] = $userRead->name .' - '.$userRead->laboratory[0]->name;
            } else {
                $ut[$user] = $userRead->name .' - nie wybrano laboratorium';
            }
        }
        unset($user);
        $parameter = CategoriesParameters::where('id', $parameterId)->first();
//        dd($result);
        return view('suppliers/singleParameter', ['supplier' => $supplier, 'results' => $result, 'pool' => $pool, 'ut' => $ut, 'parameter' => $parameter]);
    }

    public function listPools($poolId, $supplierId)
    {
        $supplier = Supplier::where('id', $supplierId)->first();
        $result = SupplierPoolQuestion::getResultForSinglePool($poolId, $supplier);
        $pool = Pool::where('id', $poolId)->first();
        $ut = [];
        foreach ($result['users'] as &$user) {
            $userRead = User::where('id', $user)->first();
            if (isset($userRead->laboratory[0]) && !empty($userRead->laboratory[0])) {
                $ut[$user] = $userRead->name .' - '.$userRead->laboratory[0]->name;
            } else {
                $ut[$user] = $userRead->name .' - nie wybrano laboratorium';
            }
        }
        unset($user);

        return view('suppliers/listPools', ['supplier' => $supplier, 'results' => $result, 'pool' => $pool, 'ut' => $ut]);
    }

    public function singlePool($poolId, $supplierId, $userId)
    {
        $supplier = Supplier::where('id', $supplierId)->first();
        $result = SupplierPoolQuestion::getResultForSinglePool($poolId, $supplier);
        $pool = Pool::where('id', $poolId)->first();
        $ut = [];
        foreach ($result['users'] as &$user) {
            $userRead = User::where('id', $user)->first();
            if (isset($userRead->laboratory[0]) && !empty($userRead->laboratory[0])) {
                $ut[$user] = $userRead->name .' - '.$userRead->laboratory[0]->name;
            } else {
                $ut[$user] = $userRead->name .' - nie wybrano laboratorium';
            }
        }
        unset($user);
//        dd($result);
        return view('suppliers/singlePool', ['supplier' => $supplier, 'results' => $result, 'pool' => $pool, 'ut' => $ut, 'userIdGlobal' => $userId]);
    }

    /**
     * list supplier pools
     * @param $supplierId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function listSupplierPools($supplierId)
    {
        $supplier = Supplier::where('id', $supplierId)->first();

        return view('suppliers/listSupplierPools', ['supplier' => $supplier]);
    }
}
