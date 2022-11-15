<?php

namespace App\Models;
// namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;



class Task extends Model 
{

    use Authenticatable, Authorizable;
/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'desc','assigned_to','assigned_by','due_date','status','assigned_to_name','assigned_by_name',
    ];
/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];
/**
       * If user email have changed email verification is required
       */
//     protected static function boot()
//     {
//         parent::boot();
//         static::saved(function ($model) {
//             if( $model->isDirty('desc') ) {
//                 $model->setAttribute('status', 'Assigned');
//             }
//         });
//    }
}