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
}
