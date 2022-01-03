<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 03/01/2022
 * Time: 22:29
 */

namespace App\Http\Controllers;

use App\Models\Categories;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Models\Pool;

class PoolsController extends VoyagerBaseController {

    public function categories($id)
    {

        if (!empty(request()->all())) {
            $data = request()->all();
            dd($data);
        }
        $categoriesGet = Categories::where('pool_id', $id)->get();
        $categories = [];
        if (!empty($categoriesGet)) {
            foreach ($categoriesGet as $single) {
                $categoriesParams = $single->categoriesParameters;
                $categories[$single->id] = [
                    'id' => $single->id,
                    'name' => $single->name,
                    'parameters' => $categoriesParams->toArray()
                ];
            }
        }

        return view("pools/categories", ['categories' => $categories]);
    }
}
