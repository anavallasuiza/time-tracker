<?php


namespace App\Http\Controllers;

use App\Libs\Shell;
use App\Libs\Utils;
use Illuminate\Http\Request;
use Session;
use Config;

class MaintenanceController extends BaseController
{
    public function sync()
    {
        $synEnabled = config('time-tracker-sync.path')===false?false:true;

        return view('web.pages.maintenance.sync')->with('synEnabled',$synEnabled);
    }

    public function doSync(Request $request)
    {
        $response = $this->executeSync();
        return view('web.pages.maintenance.sync')
            ->with('response', $response)
            ->with('action', $request->input('action'));

    }

    /**
     * @return array|bool|mixed
     */
    private function executeSync()
    {
        set_time_limit(0);

        $syncPhp = config('time-tracker-sync.path');

        $Shell = new Shell();

        $cmd = sprintf('php -f "%s" showdb=false response=json',$syncPhp);

        if (!$this->getLoggedUser()->isAdmin()) {
            $cmd .= ' user="' . $this->getLoggedUser()->user . '"';
        }

        $Shell->exec($cmd);

        $log = $Shell->getLog();
        $log = end($log);

        $response = false;
        if ($log['success']) {
            Session::flash('flash-message', [
                'status' => 'success',
                'message' => _('Databases synchronized successfully')
            ]);

            $response = Utils::object2array(json_decode(trim($log['response'])));
        }

        if ($response === false) {
            Session::flash('flash-message', [
                'status' => 'danger',
                'message' => _('Error synchronizing databases')
            ]);

            $response = [
                'error' => [
                    'status' => 'danger',
                    'message' => $response
                ]
            ];
        }

        return $response;
    }
}