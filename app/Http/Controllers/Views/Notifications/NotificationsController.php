<?php

namespace Biigle\Http\Controllers\Views\Notifications;

use Biigle\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * Shows the notification center.
     *
     * @param Request $request
     * @param Guard $auth
     */
    public function index(Request $request, Guard $auth)
    {
        $user = $auth->user();
        $all = (boolean) $request->input('all', false);
        $notifications = $all ? $user->notifications : $user->unreadNotifications;

        foreach ($notifications as $n) {
            $n->created_at_diff = $n->created_at->diffForHumans();
        }

        return view('notifications.index', [
            'all' => $all,
            'notifications' => $notifications,
        ]);
    }
}
