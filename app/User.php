<?php

namespace App;

use App\Models\Department;
use App\Models\Laboratory;
use App\Models\Pool;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function laboratory ()
    {
        return $this->belongsToMany(Laboratory::class, 'user_laboratories', 'user_id', 'laboratory_id');
    }

    public function departments() {
        return $this->belongsToMany(Department::class, 'users_departments', 'user_id', 'department_id');
    }

    /**
     * get pools for user
     * @param $userId
     * @return array
     */
    public static function getPoolsForUser($userId)
    {
        $user = self::where('id', $userId)->first();
        $departmentsId = $user->departments->pluck('id')->toArray();
        $pools = [];
        if (!empty($departmentsId)) {
            $poolsId = DB::table('pools_departments')->whereIn('department_id', $departmentsId)->groupBy('pool_id')->pluck('pool_id')->toArray();
            if (!empty($poolsId)) {
                $pools = Pool::whereIn('id', $poolsId)->get();
            }
        }

        return $pools;
    }
}
