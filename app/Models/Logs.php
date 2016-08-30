<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model {
    protected $table = 'logs';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsTo('App\Models\Users', 'id_users');
    }
}