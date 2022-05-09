<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 02/02/2022
 * Time: 19:46
 */

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Supplier;
use App\Models\SuppliersContacts;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class SuppliersController extends VoyagerBaseController {

    public function addContact($id)
    {
        $data = request()->all();
        $message = '';
        if (!empty($data)) {
            if (!empty($data['supplier_id']) && !empty($data['name']) && !empty($data['department_id'])) {
                $supplierContact = new SuppliersContacts();
                $supplierContact->supplier_id = $data['supplier_id'];
                $supplierContact->department_id = $data['department_id'];
                $supplierContact->name = $data['name'];
                $supplierContact->email = $data['email'] ?? '';
                $supplierContact->phone = $data['phone'] ?? '';
                $supplierContact->stanowisko = $data['stanowisko'] ?? '';

                if ($supplierContact->save()) {
                    return redirect(url('/admin/suppliers/'.$id))->with([
                            'message'    => 'Poprawnie dodałem kontakt dla dostawcy',
                            'alert-type' => 'success',
                        ]
                    );
                }
            } else {
                $message = "Proszę uzupełnić pola";
            }
        }
        $supplier = Supplier::where('id', $id)->first();
        $departments = Department::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        return view("suppliers/addContact", ['departments' => $departments, 'supplier_id' => $id, 'supplier' => $supplier, 'message' => $message]);
    }

    public function removeContact($id, $contactId)
    {
        $check = SuppliersContacts::where('id', $contactId)->first();
        if ($check) {
            if ($check->supplier_id != $id) {
                return redirect(url('/admin/suppliers/'.$id))->with([
                        'message'    => 'Nie można usunąć kontaktu nieprzypisanego do dostawcy',
                        'alert-type' => 'error',
                    ]
                );
            } else {
                if ($check->delete()) {
                    return redirect(url('/admin/suppliers/'.$id))->with([
                            'message'    => 'Poprawnie usunąłem kontakt dla dostawcy',
                            'alert-type' => 'success',
                        ]
                    );
                }
            }
        } else {
            return redirect(url('/admin/suppliers/'.$id))->with([
                    'message'    => 'Nie znaleziono kontaktu',
                    'alert-type' => 'error',
                ]
            );
        }
    }

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];



        $searchNames = [];
        if ($dataType->server_side) {
            $searchNames = $dataType->browseRows->mapWithKeys(function ($row) {
                return [$row['field'] => $row->getTranslatedAttribute('display_name')];
            });
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            $query = $model::select($dataType->name.'.*');

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query->{$dataType->scope}();
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';

                $searchField = $dataType->name.'.'.$search->key;
                if ($row = $this->findSearchableRelationshipRow($dataType->rows->where('type', 'relationship'), $search->key)) {
                    $query->whereIn(
                        $searchField,
                        $row->details->model::where($row->details->label, $search_filter, $search_value)->pluck('id')->toArray()
                    );
                } else {
                    if ($dataType->browseRows->pluck('field')->contains($search->key)) {
                        $query->where($searchField, $search_filter, $search_value);
                    }
                }
            }

            if (isAdmin() || isSuperAdmin() || isBiuro()) {

            } else {
                // szukam tylko odpowiednich
                $user = Auth::user();
                $laboratories = $user->laboratory;
                $allowedLabs = [];
                if ($laboratories) {
                    foreach ($laboratories as $laboratory) {
                        $allowedLabs[] = $laboratory->id;
                    }
                }
                $suppliersIds = DB::table('suppliers_laboratories')->whereIn('laboratory_id', $allowedLabs)->pluck('supplier_id')->toArray();
                $query->whereIn('id', $suppliersIds);
            }

            $row = $dataType->rows->where('field', $orderBy)->firstWhere('type', 'relationship');
            if ($orderBy && (in_array($orderBy, $dataType->fields()) || !empty($row))) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                if (!empty($row)) {
                    $query->select([
                        $dataType->name.'.*',
                        'joined.'.$row->details->label.' as '.$orderBy,
                    ])->leftJoin(
                        $row->details->table.' as joined',
                        $dataType->name.'.'.$row->details->column,
                        'joined.'.$row->details->key
                    );
                }

                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }

        // Define list of columns that can be sorted server side
        $sortableColumns = $this->getSortableColumns($dataType->browseRows);

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortableColumns',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn'
        ));
    }
}
