<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Pool extends Model
{
    public function categories()
    {
        return $this->hasMany(Categories::class, 'pool_id');
    }

    public static $statuses = [
        '1' => 'Robocza',
        '2' => 'Aktywna',
        '3' => 'Nieaktywna'
    ];

    public static function getStatuses()
    {
        return self::$statuses;
    }
}
