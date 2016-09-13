<?php

namespace App\Database\Repositories;

use ANavallaSuiza\Laravel\Database\Repository\Eloquent\Repository;
use App\Database\Models\Activity;
use App\Database\Models\Estimation;
use App\Database\Models\Tag;
use App\Exceptions\ModelException;
use Illuminate\Database\Eloquent\Collection;
use ModelManager;

class ActivityRepository extends Repository
{

    private $editableAttributes = [
        'name',
        'archived'
    ];

    /**
     * @return Activity
     */
    public function getModel()
    {
        return parent::getModel();
    }

    /**
     * @return Collection
     */
    public function getUsers()
    {
        return $this->getModel()->orderBy('name', 'ASC')->get();
    }

    public function persist(array $input)
    {
        $activity = new Activity();
        foreach ($this->editableAttributes as $attribute) {
            if (isset($input[$attribute])) {
                $activity->setAttribute($attribute, $input[$attribute]);
            }
        }

        if(isset($input['id_clients']))
        {
            $activity->id_clients = $input['id_clients']!=-1?$input['id_clients']:null;
        }

        if (!$activity->save()) {
            throw new ModelException(_('Impossible to add the activity'));
        }
        /** @var TagRepository $tagsRepo */
        $tagsRepo = ModelManager::getRepository(Tag::class);
        $tags = $tagsRepo->getTagsWithoutEstimations();
        $estimations = $input['tags'];

        $total = 0;

        foreach ($tags as $tag) {
            if (empty((int)($hours = $estimations[$tag->id]))) {
                continue;
            }

            $total += $hours;

            Estimation::create([
                'hours' => $hours,
                'id_activities' => $activity->id,
                'id_tags' => $tag->id
            ]);
        }

        $activity->total_hours = $total;

        if (!$activity->save()) {
            throw new ModelException(_('Impossible to add the estimations'));
        }

        return $activity;
    }

    /**
     * @param $activityId
     * @return Activity
     */
    public function getActivity($activityId)
    {
        return $this->getModel()->whereId($activityId)->firstOrFail();
    }

    /**
     * @param $activityId
     * @param $name
     * @param $archived
     * @param $estimations
     * @param int $client
     * @return Activity
     * @throws ModelException
     */
    public function updateActivity($activityId, $name, $archived, array $estimations, $client=-1)
    {
        $activity = $this->getActivity($activityId);


        foreach ($activity->estimations as $estimation)
        {
            $estimation->delete();
        }

        $activity->name = $name;
        $activity->archived = $archived;

        $activity->id_clients = $client!=-1?$client:null;

        /** @var TagRepository $tagsRepo */
        $tagsRepo = ModelManager::getRepository(Tag::class);
        $tags = $tagsRepo->getTagsWithoutEstimations();

        $total = 0;

        foreach ($tags as $tag) {
            if (empty((int)($hours = $estimations[$tag->id]))) {
                continue;
            }

            $total += $hours;

            Estimation::create([
                'hours' => $hours,
                'id_activities' => $activity->id,
                'id_tags' => $tag->id
            ]);
        }

        $activity->total_hours = $total;

        if (!$activity->save()) {
            throw new ModelException(_('Impossible to update the activity'));
        }

        return $activity;
    }
}