<?php
/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 03/01/2022
 * Time: 22:34
 */
namespace App\Actions;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Facades\Voyager;

class SuppliersAction extends AbstractAction
{

    public function getTitle()
    {
        return "Ankiety";
    }

    public function getIcon()
    {
        return '';
    }

    public function getDefaultRoute()
    {
        return route('suppliers.pools', array("id"=>$this->data->{$this->data->getKeyName()}));
    }
    public function getAttributes()
    {
        // Action button class
        return [
            'class' => 'btn btn-sm btn-primary ',
        ];
    }
    public function shouldActionDisplayOnDataType()
    {
        // show or hide the action button, in this case will show for posts model
        $check = $this->dataType->slug == 'suppliers';

        return ($check && (isAdmin() || isSuperAdmin() || isDyrektorM()));
    }
}
