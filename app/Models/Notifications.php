<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Notifications
 *
 * @mixin \Eloquent
 */
class Notifications extends Model
{
    protected $table = 'notifications';

    protected $guarded = ['id'];
}
