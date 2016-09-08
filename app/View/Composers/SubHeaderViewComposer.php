<?php


namespace App\View\Composers;


use App\Database\Models\Notification;

class SubHeaderViewComposer
{
    public function compose($view)
    {
        $unreadNotifications = Notification::whereRead(false)
            ->get();

        $view->with('unreadNotifications', $unreadNotifications);
    }
}