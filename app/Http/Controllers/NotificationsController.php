<?php


namespace App\Http\Controllers;


use App\Database\Models\Notification;

class NotificationsController extends BaseController
{

    public function index()
    {
        $notifications = Notification::whereRead(false)
            ->orderBy('created_at', 'DESC')
            ->get();


        return view('web.pages.notifications.index')->with('notifications', $notifications);
    }

    public function read($id)
    {
        $notification = Notification::whereId($id)->firstOrFail();
        $notification->read = true;
        $notification->save();

        return redirect(route('v2.notifications.index'));
    }

}