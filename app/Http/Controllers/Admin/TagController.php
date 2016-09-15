<?php


namespace App\Http\Controllers\Admin;


use App\Database\Repositories\ClientRepository;
use App\Http\Controllers\Forms\Base;
use App\Http\Controllers\Forms\Tag as TagForm;
use App\Http\Controllers\BaseController;
use App\Http\Requests\TagRequest;

class TagController extends BaseController
{

    public function add()
    {
        $form = (new TagForm())->edit();

        return view('web.pages.edit.tag')->with('form', $form)->with('formHeader', _('New tag'))
            ->with('action', url(route('v2.edit.tag.add')));

    }

    public function postAdd(TagRequest $request)
    {
        $this->tagsRepo->persist($request->input());
        return redirect()->route('v2.edit.index');
    }


    public function edit($tagId)
    {
        $tag = $this->tagsRepo->getTag($tagId);

        /** @var Base $form */
        $form = (new TagForm())->edit();

        $form->load($tag);

        return view('web.pages.edit.client')->with('form', $form)->with('formHeader', $tag->name)
            ->with('action', url(route('v2.edit.tag.edit', ['id' => $tag->id])));

    }

    public function postEdit($tagId, TagRequest $request)
    {
        /** @var ClientRepository $clientsRepo */
        $this->tagsRepo->updateTagName($tagId, $request->get('name'));
        return redirect()->route('v2.edit.index');
    }
}