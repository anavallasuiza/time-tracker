<?php


namespace App\Http\Controllers\V2;


use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function index()
    {
        return redirect()->route('login');
    }
}