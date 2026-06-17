<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'type',
        'value',
        'max_discount',
        'min_transaction',
        'expiry_date',
        'is_new_user_only',
        'badge_text',
        'icon',
        'theme_color'
    ];
}
