<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 02/02/2022
 * Time: 19:50
 */

class SuppliersContacts extends Model
{
    public $table = 'suppliers_contacts';

    public $fillable = ['supplier_id', 'department_id', 'name', 'email', 'phone', 'stanowisko'];
}
