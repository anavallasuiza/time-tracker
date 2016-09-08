<?php

namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Notifications
 *
 * @mixin \Eloquent
 */
class Notification extends Model
{
    protected $table = 'notifications';

    protected $guarded = ['id'];
}
