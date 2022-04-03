<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 03/01/2022
 * Time: 22:29
 */

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\CategoriesParameters;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use App\Models\Pool;

class PoolsController extends VoyagerBaseController {

    public function categories($id)
    {
        $categoriesGet = Categories::where('pool_id', $id)->get();
        $categories = [];
        if (!empty($categoriesGet)) {
            foreach ($categoriesGet as $i=> $single) {
                $categoriesParams = $single->categoriesParameters;
                $categories[$i] = [
                    'id' => $single->id,
                    'name' => $single->name,
                    'is_requested' => $single->is_requested,
                    'parameters' => $categoriesParams->toArray()
                ];
            }
        }

        return view("pools/categories", ['categories' => $categories, 'id' => $id]);
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
                        if (isset($single['is_requested']) && $single['is_requested']) {
                            $category->is_requested = 1;
                        } else {
                            $category->is_requested = 0;
                        }
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
