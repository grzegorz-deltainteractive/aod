<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by Grzegorz Możdżeń <grzegorz.mozdzen@oxm.pl>
 * Date: 13/02/2022
 * Time: 17:57
 */
class PoolSupplier extends Model
{
    public $table = 'pools_suppliers';

    public $fillable = ['pool_id', 'supplier_id'];
}
