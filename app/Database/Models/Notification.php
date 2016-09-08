<?php

namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Notifications
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property boolean $read
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Notification whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Notification whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Notification whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Notification whereRead($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Notification whereUpdatedAt($value)
 */
class Notification extends Model
{
    protected $table = 'notifications';

    protected $guarded = ['id'];
}
