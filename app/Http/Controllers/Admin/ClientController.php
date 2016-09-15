<?php


namespace App\Http\Controllers\Admin;


use App\Database\Repositories\ClientRepository;
use App\Http\Controllers\Forms\Base;
use App\Http\Controllers\Forms\Client as ClientForm;
use App\Http\Controllers\BaseController;
use App\Http\Requests\ClientRequest;

class ClientController extends BaseController
{

    public function add()
    {
        $form = (new ClientForm())->edit();

        return view('web.pages.edit.client')->with('form', $form)->with('formHeader', _('New client'))
            ->with('action', url(route('v2.edit.client.add')));

    }

    public function postAdd(ClientRequest $request)
    {
        /** @var ClientRepository $clientsRepo */
        $this->clientsRepo->persist($request->input());
        return redirect()->route('v2.edit.index');
    }


    public function edit($clientId)
    {
        $client = $this->clientsRepo->getClient($clientId);

        /** @var Base $form */
        $form = (new ClientForm())->edit();

        $form->load($client);

        return view('web.pages.edit.client')->with('form', $form)->with('formHeader', $client->name)
            ->with('clientActivities', $client->activities)
            ->with('action', url(route('v2.edit.client.edit', ['id' => $client->id])));

    }

    public function postEdit($clientId, ClientRequest $request)
    {
        /** @var ClientRepository $clientsRepo */
        $this->clientsRepo->updateClientName($clientId, $request->get('name'));
        return redirect()->route('v2.edit.index');
    }
}