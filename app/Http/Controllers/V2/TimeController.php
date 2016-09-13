<?php


namespace App\Http\Controllers\V2;

use App\Database\Models\Fact;
use App\Database\Models\Log;
use App\Http\Requests\FactRequest;
use App\Libs\Utils;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Input;
use Response;
use DB;

class TimeController extends BaseController
{

    public function index()
    {
        $filters = Utils::filters();
        $facts = Fact::with(['activities'])
            ->with(['tags'])
            ->with(['users']);

        /** @var Builder $facts */
        $facts = Fact::filter($facts, $filters);

        if (Input::get('export') === 'csv') {
            return $this->downloadCsv($facts->get());
        }

        $rows = in_array((int)Input::get('rows'), [-1, 20, 50, 100], true) ? (int)Input::get('rows') : 20;

        /** @var Fact $facts */
        if ($rows === -1) {
            $facts = $facts->get();
        } else {
            $facts = $facts->paginate($rows);
        }

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

    public function updateFact(FactRequest $request)
    {
        $fact = Fact::where('id', '=', $request->input('id'));

        if (!$this->getLoggedUser()->isAdmin()) {
            $fact->where('id_users', '=', $this->getLoggedUser()->id);
        }

        $fact = $fact->firstOrFail();

        list($start, $end, $total) = Utils::startEndTime($request->input('start'), $request->input('end'),$request->input('time'));

        $fact->start_time = $start;
        $fact->end_time = $end;
        $fact->total_time = $total;
        $fact->description = Input::get('description');
        $fact->id_activities = (int)Input::get('activity');

        try {
            $fact->save();
        } catch (Exception $e) {
            throw new Exception(sprintf(_('Error updating fact: %s'), $e->getMessage()));
        }

        DB::table('facts_tags')
            ->where('id_facts', '=', $fact->id)
            ->delete();

        DB::table('facts_tags')->insert([
            'id_facts' => $fact->id,
            'id_tags' => (int)Input::get('tag')
        ]);

        Log::create([
            'description' => _('Updated fact'),
            'date' => date('Y-m-d H:i:s'),
            'id_facts' => $fact->id,
            'id_users' => $this->getLoggedUser()->id
        ]);

        return Response::json([
            'id' => $fact->id
        ]);
    }

    public function addFact(FactRequest $request)
    {
        list($start, $end, $total) = Utils::startEndTime($request->input('start'), $request->input('end'), $request->input('time'));

        $overwrite = Fact::where('id_users', '=', $this->getLoggedUser()->id)
            ->where('start_time', '<', $start)
            ->where('end_time', '>', $end)
            ->first();

        if ($overwrite) {
            throw new Exception(_('This fact ovewrite on same time other different fact'));
        }

        try {
            $fact = Fact::create([
                'start_time' => $start,
                'end_time' => $end,
                'total_time' => $total,
                'description' => trim($request->input('description')),
                'id_activities' => (int)$request->input('activity'),
                'id_users' => $this->getLoggedUser()->id
            ]);
        } catch (Exception $e) {
            throw new Exception(sprintf(_('Error creating fact: %s'), $e->getMessage()));
        }

        DB::table('facts_tags')
            ->where('id_facts', '=', $fact->id)
            ->delete();

        DB::table('facts_tags')->insert([
            'id_facts' => $fact->id,
            'id_tags' => (int)$request->input('tag')
        ]);

        Log::create([
            'description' => _('Created fact'),
            'date' => date('Y-m-d H:i:s'),
            'id_facts' => $fact->id,
            'id_users' => $this->getLoggedUser()->id
        ]);

        return Response::json([
            'id' => $fact->id
        ]);
    }
}