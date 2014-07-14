<?php
namespace App\Models;

use Eloquent;

class Categories extends Eloquent {
    protected $table = 'categories';
    protected $guarded = ['id'];

    public $timestamps = false;
}