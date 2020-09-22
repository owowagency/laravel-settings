<?php

namespace OwowAgency\LaravelNotifications\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Model;
use OwowAgency\LaravelNotifications\Resources\NotificationResource;

class NotificationController extends Controller
{
    /**
     * Paginate notifications that belongs to the notifiable.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginateForNotifiable(Model $notifiable): JsonResponse
    {
        dd('a');
        
        $this->authorize('viewNotifications', $notifiable);

        $notifications = $notifiable->notifications()->latest()->paginate();

        $resources = NotificationResource::collection($notifications);

        return new JsonResponse($resources);
    }
}
