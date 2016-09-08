<?php


namespace App\Http\Controllers;


use App\Database\Repositories\ClientRepository;
use App\Http\Requests\ClientRequest;
use App\Database\Models\Client;
use ModelManager;

class ClientController extends Base
{
    /**
     * @var ClientRepository
     */
    protected $clientsRepo;

    public function __construct()
    {
        $this->clientsRepo = ModelManager::getRepository(Client::class);
        parent::__construct();

    }

    public function add()
    {
        $form = (new Forms\Client())->edit();

        return \View::make('base')->nest('body', 'client', [
            'form' => $form,
            'formHeader' => _('New client')
        ]);
    }

    public function postAdd(ClientRequest $request)
    {
        /** @var ClientRepository $clientsRepo */
        $this->clientsRepo->persist($request->input());
        return redirect()->route('edit.show');
    }


    public function edit($clientId)
    {
        $client = $this->clientsRepo->getClient($clientId);

        $form = (new Forms\Client())->edit();

        $form->load($client);

        return \View::make('base')->nest('body', 'client', [
            'form' => $form,
            'formHeader' => $client->name,
            'activities' => $client->activities
        ]);
    }

    public function postEdit($clientId, ClientRequest $request)
    {
        /** @var ClientRepository $clientsRepo */
        $this->clientsRepo->updateClientName($clientId,$request->get('name'));
        return redirect()->route('edit.show');
    }
}