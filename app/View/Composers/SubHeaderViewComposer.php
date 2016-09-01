<?php


namespace App\View\Composers;


use App\Models\Notifications;

class SubHeaderViewComposer
{
    public function compose($view)
    {
        $unreadNotifications = Notifications::whereRead(false)
            ->get();

        $view->with('unreadNotifications', $unreadNotifications);
    }
}