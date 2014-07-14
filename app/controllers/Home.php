<?php
namespace App\Controllers;

use \App\Models, \View;

class Home extends Base {

	public function index()
	{
        $facts = Models\Facts::orderBy('id', 'start_time')->with(['activities'])->with(['users']);

        return View::make('base')->nest('body', 'index', [
            'facts' => $facts
        ]);
	}
}
