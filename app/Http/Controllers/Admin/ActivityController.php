<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Forms\Activity as ActivityForm;
use App\Http\Controllers\BaseController;
use App\Http\Requests\ActivityRequest;

class ActivityController extends BaseController
{

    public function add()
    {
        $form = (new ActivityForm())->edit();
        $clients = $this->clientsRepo->getClients();

        $clientsArray = [-1 => 'No client'];
        foreach ($clients as $client) {
            $clientsArray[$client->id] = $client->name;
        }
        $form['id_clients']->options($clientsArray);


        $activityTags = $this->tagsRepo->getTagsWithoutEstimations();

        return view('web.pages.edit.activity')->with('form', $form)->with('formHeader', _('New activity'))
            ->with('activityTags', $activityTags)
            ->with('action', url(route('edit.activity.add')));

    }

    public function postAdd(ActivityRequest $request)
    {
        $this->activityRepo->persist($request->input());
        return redirect()->route('edit.index');
    }


    public function edit($activityId)
    {
        $activity = $this->activityRepo->getActivity($activityId);
        $activityTags = $this->tagsRepo->getByActivityWithEstimations($activity->id);
        /** @var \App\Http\Controllers\Forms\Base $form */
        $form = (new ActivityForm())->edit();

        $clients = $this->clientsRepo->getClients();

        $clientsArray = [-1 => 'No client'];
        foreach ($clients as $client) {
            $clientsArray[$client->id] = $client->name;
        }
        $form['id_clients']->options($clientsArray);

        $form->load($activity);

        return view('web.pages.edit.activity')->with('form', $form)->with('formHeader', $activity->name)
            ->with('activityTags', $activityTags)
            ->with('action', url(route('edit.activity.edit', ['id' => $activity->id])));

    }

    public function postEdit($activityId, ActivityRequest $request)
    {
        $this->activityRepo->updateActivity($activityId, $request->input('name'), $request->input('archived'),
            $request->input('tags'), $request->input('id_clients'));
        return redirect()->route('edit.index');
    }
}