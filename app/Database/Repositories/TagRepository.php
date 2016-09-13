<?php


namespace App\Database\Repositories;

use ANavallaSuiza\Laravel\Database\Repository\Eloquent\Repository;
use App\Exceptions\ModelException;
use App\Database\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagRepository extends Repository
{

    /**
     * @return Tag
     */
    public function getModel()
    {
        return parent::getModel();
    }

    /**
     * @return Collection
     */
    public function getTags()
    {
        return $this->getModel()->orderBy('name', 'ASC')->get();
    }

    public function persist(array $input)
    {
        $tag = new Tag();
        $tag->name = $input['name'];
        if (!$tag->save()) {
            throw new ModelException(_('Impossible to add the tag'));
        }
        return $tag;
    }

    /**
     * @param $tagId
     * @return Tag
     */
    public function getTag($tagId)
    {
        return $this->getModel()->whereId($tagId)->firstOrFail();
    }

    public function updateTagName($tagId,$name)
    {
        $tag = $this->getTag($tagId);
        $tag->name = $name;
        if(!$tag->save())
        {
            throw new ModelException(_('Impossible to update the tag'));
        }
        return $tag;
    }

    /**
     * @param $activityId
     * @return Collection
     */
    public function getByActivityWithEstimations($activityId)
    {
        $tags = $this->getModel()->orderBy('name', 'ASC')->with(['estimations' => function ($query) use ($activityId) {
            $query->where('id_activities', '=', $activityId);
        }])->get();

        return $tags;
    }

    /**
     * @return Collection
     */
    public function getTagsWithoutEstimations()
    {
        $tags = $this->getModel()->orderBy('name', 'ASC')->get();
        foreach ($tags as $tag)
        {
            $tag->estimations = [];
        }

        return $tags;
    }
}
