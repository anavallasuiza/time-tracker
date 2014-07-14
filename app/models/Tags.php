<?php
namespace App\Models;

use Eloquent;

class Tags extends Eloquent {
    protected $table = 'tags';
    protected $guarded = ['id'];

    public $timestamps = false;
}