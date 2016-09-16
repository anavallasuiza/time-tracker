<?php
namespace App\Database\Models;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\HasCustomRepository;
use App\Database\Repositories\ActivityRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Database\Models\Activities
 *
 * @property-read \App\Database\Models\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Fact[] $facts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Database\Models\Estimation[] $estimations
 * @mixin \Eloquent
 * @property integer $id
 * @property string $name
 * @property integer $total_hours
 * @property boolean $archived
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $id_clients
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Activity whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Activity whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Activity whereTotalHours($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Activity whereArchived($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Activity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Database\Models\Activity whereIdClients($value)
 */
class Activity extends Model implements HasCustomRepository {
    protected $table = 'activities';
    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_clients');
    }

    public function facts()
    {
        return $this->hasMany(Fact::class, 'id_activities', 'id');
    }

    public function estimations()
    {
        return $this->hasMany(Estimation::class, 'id_activities', 'id');
    }

    /**
     * @return bool
     */
    public function isArchived()
    {
        return $this->archived==0?false:true;
    }

    /**
     * @return bool
     */
    public function hasClient()
    {
        return !empty($this->id_clients);
    }

    public function repository()
    {
        return ActivityRepository::class;
    }
}