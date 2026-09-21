<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\VolunteerApplication;

class NotificationController extends Controller
{
    // GET /api/notifications — combines volunteer applications and contact
    // messages into a single feed sorted newest-first, since both are
    // "someone reached out" events the admin needs to see in one place.
    public function index()
    {
        $volunteers = VolunteerApplication::orderByDesc('created_at')->get()->map(fn ($v) => [
            'type' => 'volunteer',
            'id' => $v->id,
            'title' => $v->name,
            'excerpt' => "Wants to help with {$v->area} · {$v->availability}",
            'read' => $v->read,
            'created_at' => $v->created_at,
            // Full detail included here so the dashboard can expand a notification
            // inline without a second request — this IS "seeing the sent form".
            'detail' => [
                'email' => $v->email, 'phone' => $v->phone,
                'area' => $v->area, 'availability' => $v->availability, 'message' => $v->message,
            ],
        ]);

        $messages = ContactMessage::orderByDesc('created_at')->get()->map(fn ($m) => [
            'type' => 'message',
            'id' => $m->id,
            'title' => $m->name,
            'excerpt' => $m->subject ?: (strlen($m->message) > 60 ? substr($m->message, 0, 60).'…' : $m->message),
            'read' => $m->read,
            'created_at' => $m->created_at,
            'detail' => [
                'email' => $m->email, 'phone' => $m->phone,
                'subject' => $m->subject, 'message' => $m->message,
            ],
        ]);

        return $volunteers->concat($messages)->sortByDesc('created_at')->values();
    }

    // GET /api/notifications/unread-count — cheap enough to poll for the
    // bell icon's badge without pulling every record just to count them.
    public function unreadCount()
    {
        return response()->json([
            'count' => VolunteerApplication::where('read', false)->count()
                + ContactMessage::where('read', false)->count(),
        ]);
    }
}
