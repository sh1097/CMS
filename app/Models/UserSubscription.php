<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subscription_name',
        'subscription_type',
        'subscription_price',
        'subscription_start_date',
        'subscription_end_date',
        'subscription_status',
        'payment_id',
        'payment_detail',
    ];


    public function userDetail(){

        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
