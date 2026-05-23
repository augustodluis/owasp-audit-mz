<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(NotificationService $service)
    {
        if (request()->wantsJson()) {
            return $service->pending(Auth::user());
        }
        $notifications = Auth::user()->notifications()->latest()->paginate(30);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->update(['read_flag' => true]);
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->noContent();
        }
        return back();
    }

    public function apiIndex(NotificationService $service)
    {
        return $service->pending(Auth::user());
    }

    public function apiMarkRead(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->update(['read_flag' => true]);
        return response()->noContent();
    }
}
