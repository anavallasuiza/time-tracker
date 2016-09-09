<?php


namespace App\Http\Controllers\V2;

use App\Database\Models\Activity;
use App\Database\Models\Client;
use App\Database\Models\Tag;
use App\Database\Models\User;
use App\Database\Repositories\ClientRepository;
use App\Http\Controllers\Controller;
use Auth;
use View;
use ModelManager;

class BaseController extends Controller
{
    /**
     * @var ClientRepository
     */
    protected $clientsRepo;

    /**
     * @var User
     */
    protected $loggedUser;

    /**
     * BaseController constructor.
     */
    public function __construct( ) {
        $this->loggedUser = Auth::user();
        $this->clientsRepo = ModelManager::getRepository(Client::class);

    }



    /**
     * @return User
     */
    protected function getLoggedUser()
    {
        return $this->loggedUser;
    }

    protected function share()
    {
        if ($this->getLoggedUser()->isAdmin()) {
            $users = User::orderBy('name', 'ASC')->get();
        } else {
            $users = User::where('id', '=', $this->getLoggedUser()->id)->get();
        }

        $activities = Activity::where('archived', '=', 0)
            ->orderBy('name', 'ASC')
            ->get();

        View::share([
            'users' => $users,
            'activities' => $activities,
            'user' => $this->getLoggedUser(),
            'tags' => Tag::orderBy('name', 'ASC')->get()
        ]);
    }
}