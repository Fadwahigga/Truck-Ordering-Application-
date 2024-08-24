<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pickup_location',
        'delivery_location',
        'size',
        'weight',
        'pickup_time',
        'delivery_time',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
