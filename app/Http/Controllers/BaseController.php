<?php


namespace App\Http\Controllers;

use App\Database\Models\Activity;
use App\Database\Models\Client;
use App\Database\Models\Fact;
use App\Database\Models\Tag;
use App\Database\Models\User;
use App\Database\Repositories\ActivityRepository;
use App\Database\Repositories\ClientRepository;
use App\Database\Repositories\FactRepository;
use App\Database\Repositories\TagRepository;
use App\Database\Repositories\UserRepository;
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
     * @var UserRepository
     */
    protected $usersRepo;

    /**
     * @var TagRepository
     */
    protected $tagsRepo;

    /** @var  ActivityRepository */
    protected $activityRepo;

    /**
     * @var FactRepository
     */
    protected $factRepository;

    /**
     * BaseController constructor.
     */
    public function __construct( ) {
        $this->loggedUser = Auth::user();
        $this->clientsRepo = ModelManager::getRepository(Client::class);
        $this->usersRepo = ModelManager::getRepository(User::class);
        $this->tagsRepo = ModelManager::getRepository(Tag::class);
        $this->activityRepo = ModelManager::getRepository(Activity::class);
        $this->factRepository = ModelManager::getRepository(Fact::class);
        $this->share();
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
            'tags' => Tag::orderBy('name', 'ASC')->get(),
        ]);
    }
}