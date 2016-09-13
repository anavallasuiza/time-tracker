<?php


namespace App\Http\Controllers\V2\Admin;


use App\Database\Models\Activity;
use App\Database\Models\Tag;
use App\Database\Models\User;
use App\Http\Controllers\V2\BaseController;

class EditController extends BaseController
{
    public function index()
    {
        if ($this->getLoggedUser()->isAdmin()) {
            $users = User::orderBy('name', 'ASC')->get();
        } else {
            $users = [];
        }


        return view('web.pages.edit.index')
            ->with('activities',Activity::orderBy('name', 'ASC')->get())
            ->with('tags', Tag::orderBy('name', 'ASC')->get())
            ->with('users',$users)
            ->with('clients',$this->clientsRepo->getClients());
    }
}