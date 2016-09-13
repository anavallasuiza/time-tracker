<?php


namespace App\Http\Controllers\V2\Admin;


use App\Http\Controllers\Forms\User as UserForm;
use App\Http\Controllers\V2\BaseController;
use App\Http\Requests\UserRequest;

class UserController extends BaseController
{
    public function add()
    {
        $form = (new UserForm())->edit();
        $form['password_confirmation'] = $form['password_repeat'];

        $form['enabled']->attr('checked', 'checked');
        $form['api_key']->val(hash('sha256', uniqid()));

        unset($form['password_repeat']);

        return view('web.pages.edit.user')->with('form', $form)->with('formHeader', _('New user'))
            ->with('action', url(route('v2.edit.user.add')));

    }

    public function postAdd(UserRequest $request)
    {
        $this->usersRepo->persist($request->input());
        return redirect()->route('v2.edit.index');
    }


    public function edit($userId)
    {
        $user = $this->usersRepo->getUser($userId);

        /** @var \App\Http\Controllers\Forms\Base $form */
        $form = (new UserForm())->edit();

        $form['password_confirmation'] = $form['password_repeat'];
        unset($form['password_repeat']);

        unset($user->password);
        $form->load($user);

        if ($this->getLoggedUser()->id === $userId) {
            unset($form['enabled']);
        }


        return view('web.pages.edit.user')->with('form', $form)->with('formHeader', $user->name)
            ->with('clientActivities', $user->activities)
            ->with('action', url(route('v2.edit.user.edit', ['id' => $user->id])));

    }

    public function postEdit($userId, UserRequest $request)
    {
        $this->usersRepo->updateUser($userId, $request->input());
        return redirect()->route('v2.edit.index');
    }
}