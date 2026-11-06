<?php

namespace App\Http\Controllers;

use App\Models\ActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Serves both the full notifications page (HTML) and the top-bar bell (JSON):
 * actions return JSON when the request asks for it, otherwise redirect back.
 */
class NotificationController extends Controller
{
    private const FILTERS = ['all', 'unread', 'create', 'update', 'delete'];

    public function index(Request $request): View|JsonResponse
    {
        $mine = $request->user()->activityNotifications();

        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => (clone $mine)->latest()->limit(30)->get(),
                'unread' => (clone $mine)->unread()->count(),
            ]);
        }

        $filter = in_array($request->query('filter'), self::FILTERS, true) ? $request->query('filter') : 'all';

        $notifications = (clone $mine)
            ->when($filter === 'unread', fn ($q) => $q->unread())
            ->when(in_array($filter, ['create', 'update', 'delete']), fn ($q) => $q->where('type', $filter))
            ->latest()
            ->get();

        return view('notifications.index', [
            'notifications' => $notifications,
            'filter' => $filter,
            'counts' => [
                'all' => (clone $mine)->count(),
                'unread' => (clone $mine)->unread()->count(),
                'create' => (clone $mine)->where('type', 'create')->count(),
                'update' => (clone $mine)->where('type', 'update')->count(),
                'delete' => (clone $mine)->where('type', 'delete')->count(),
            ],
        ]);
    }

    public function markRead(Request $request, ActivityNotification $notification): JsonResponse|RedirectResponse
    {
        $this->authorizeOwner($request, $notification);
        $notification->update(['is_read' => true]);

        return $this->respond($request);
    }

    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        $request->user()->activityNotifications()->unread()->update(['is_read' => true]);

        return $this->respond($request, 'All notifications marked as read.');
    }

    public function destroy(Request $request, ActivityNotification $notification): JsonResponse|RedirectResponse
    {
        $this->authorizeOwner($request, $notification);
        $notification->delete();

        return $this->respond($request);
    }

    public function destroyRead(Request $request): JsonResponse|RedirectResponse
    {
        $request->user()->activityNotifications()->where('is_read', true)->delete();

        return $this->respond($request, 'Read notifications removed.');
    }

    public function destroyAll(Request $request): JsonResponse|RedirectResponse
    {
        $request->user()->activityNotifications()->delete();

        return $this->respond($request, 'All notifications cleared.');
    }

    private function authorizeOwner(Request $request, ActivityNotification $notification): void
    {
        abort_unless($notification->faculty_id === $request->user()->id, 403);
    }

    private function respond(Request $request, ?string $message = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with($message ? ['status' => $message] : []);
    }
}
