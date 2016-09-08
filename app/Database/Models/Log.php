<?php
namespace App\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Logs
 *
 * @property-read \App\Database\Models\User $users
 * @mixin \Eloquent
 * @property integer $id
 * @property string $date
 * @property string $description
 * @property integer $id_activities
 * @property integer $id_facts
 * @property integer $id_tags
 * @property integer $id_users
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Log whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Log whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Log whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Log whereIdActivities($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Log whereIdFacts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Log whereIdTags($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Log whereIdUsers($value)
 */
class Log extends Model {
    protected $table = 'logs';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo(User::class, 'id_users');
    }
}