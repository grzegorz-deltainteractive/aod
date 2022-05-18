<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 03/01/2022
 * Time: 22:29
 */

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\CategoriesParameters;
use App\Models\Laboratory;
use App\Models\Supplier;
use App\Models\SupplierPoolQuestion;
use App\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Models\Pool;

class RaportsController extends VoyagerBaseController {

    public function index(Request $request)
    {

        return view("raports/index", $this->getData());
    }

    public function generateRaport(Request $request)
    {
        $data = $request->all();

        if (empty($data['suppliersIds']) || empty($data['years'])) {

            return redirect('/admin/raports')->with([
                'message'    => "Proszę wybrać dostawcę i rok",
                'alert-type' => 'error',
            ]);
        }
        $selectedSuppliers = $data['suppliersIds'];
        $selectedYears = $data['years'];
        $selectedPools = $data['poolsIds'] ?? [];

        $pools = [];
        if (!empty($selectedSuppliers)) {
            foreach ($selectedSuppliers as $supplierId) {
                $supplier = Supplier::where('id', $supplierId)->first();
                if (!empty($supplier)) {
                    $poolsRelation = $supplier->poolsRelation;
                    if (!empty($poolsRelation)) {
                        foreach ($poolsRelation as $pool) {
                            if (!array_key_exists($pool->id, $pools)) {
                                $pools[$pool->id] = $pool->name.' '.$pool->numer_procedury;
                            }
                        }
                    }
                }
            }
        }

        $return = array_merge($this->getData(), ['selectedSuppliers' => $selectedSuppliers, 'selectedYears' => $selectedYears, 'selectedPools' => $selectedPools, 'pools' => $pools]);
        if (isset($data['generatePDF']) && $data['generatePDF'] == 1) {
            try {
                view()->share($return);
                $pdf = PDF::loadView('raports/raportPdf');

//            $pdf->setWarnings(true);
                $pdf->setPaper('a4', 'portrait');
//            $pdf->save('ankieta-'.$poolId.'-'.$supplierId.'.pdf');
                return $pdf->stream('raport'.'-'.date('Y_m_d_H_i_s').'.pdf');
            } catch (\Exception $ex) {
                dd($ex);
            }
        } else {
            return view('raports/raport', $return);
        }

    }

    private function getData()
    {
        $suppliers = [];
        if (isSuperAdmin() || isAdmin() || isDyrektorM()) {
            // if logged user is admin or superadmin get all supplier list
            $suppliers = Supplier::getList(0);
        } else {
            $userId = Auth::user()->id;
            $user = User::where('id', $userId)->first();
            if (!empty($user)) {
                $laboratoriesId = $user->laboratory->pluck('id')->toArray();
                if (!empty($laboratoriesId)) {
                    $suppliersId = DB::table('suppliers_laboratories')->whereIn('laboratory_id', $laboratoriesId)->pluck('supplier_id')->toArray();
                    if (!empty($suppliersId)) {
                        if (!empty($suppliersId)) {
                            $suppliers = Supplier::getList($suppliersId);
                        }
                    }
                }
            }
        }
        $allYears = [];
        if (!empty($suppliers)) {
            foreach ($suppliers as $supplierId => $name) {
                $years = SupplierPoolQuestion::getYearsForSupplier($supplierId);
                if (!empty($years)) {
                    foreach ($years as $single) {
                        if (!in_array($single, $allYears)) {
                            $allYears[] = $single;
                        }
                    }
                }
            }
        }
        sort($allYears);

        return ['suppliersList' => $suppliers, 'years' => $allYears];
    }

    public function getPools(Request $request)
    {
        $ids = $request->get('suppliersIds', []);
        if (!empty($ids)) {
            $pools = [];
            foreach ($ids as $id) {
                $supplier = Supplier::where('id', $id)->first();
                if (!empty($supplier)) {
                    $poolsRelation = $supplier->poolsRelation;
                    if (!empty($poolsRelation)) {
                        foreach ($poolsRelation as $pool) {
                            if (!array_key_exists($pool->id, $pools)) {
                                $pools[$pool->id] = $pool->name .' '.$pool->numer_procedury;
                            }
                        }
                    }
                }
            }
            if (!empty($pools)) {
                return response()->json(['pools' => $pools, 'error' => '']);
            } else {
                return response()->json(['pools' => '', 'error' => 'Brak ankiet']);
            }
        } else {
            return response()->json(['pools' => '', 'error' => 'Brak ankiet']);
        }
    }
}
