<?php


namespace App\Http\Controllers\V2\Time;

use App\Database\Models\Fact;
use App\Http\Controllers\V2\BaseController;
use App\Libs\Utils;
use Illuminate\Support\Collection;
use Input;
use Response;

class IndexController extends BaseController
{

    public function index()
    {
        $filters = Utils::filters();
        $facts = Fact::with(['activities'])
            ->with(['tags'])
            ->with(['users']);

        /** @var Collection $facts */
        $facts = Fact::filter($facts, $filters);

        if (Input::get('export') === 'csv') {
            return $this->downloadCsv($facts);
        }

        $rows = in_array((int)Input::get('rows'), [-1, 20, 50, 100], true) ? (int)Input::get('rows') : 20;

        /** @var Fact $facts */
        if ($rows === -1) {
            $facts = $facts->get();
        } else {
            $facts = $facts->paginate($rows);
        }

        $this->share();

        return view('web.pages.time.index')->with('facts', $facts)
            ->with('total_time', Utils::sumHours($facts))
            ->with('rows', $rows)
            ->with('sort', $filters['sort'])
            ->with('filters',$filters)
            ->with('clients', $this->clientsRepo->getClients());

    }

    /**
     * @param Collection $facts
     * @return \Illuminate\Http\Response
     */
    private function downloadCsv(Collection $facts)
    {
        $date_format = $this->getLoggedUser()->isAdmin() ? 'd/m/Y H:i' : 'd/m/Y';

        $output = '"' . _('User') . '","' . _('Activity') . '","' . _('Description') . '","' . _('Tags') . '","' . _('Start time') . '","' . _('End time') . '","' . _('Total time') . '"';

        foreach ($facts as $fact) {
            /** @var Fact $fact */
            $output .= "\n" . '"' . $fact->users->name . '"'
                . ',"' . str_replace('"', "'", $fact->activities->name) . '"'
                . ',"' . str_replace('"', "'", $fact->description) . '"'
                . ',"' . str_replace('"', "'",
                    implode(', ', array_column(json_decode(json_encode($fact->tags), true), 'name'))) . '"'
                . ',"' . $fact->start_time->format($date_format) . '"'
                . ',"' . $fact->end_time->format($date_format) . '"'
                . ',"' . $fact->start_time->diff($fact->end_time)->format('%H:%I') . '"';
        }

        return Response::make($output, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . _('time-tracking.csv') . '"'
        ]);
    }
}