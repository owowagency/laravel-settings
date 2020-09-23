<?php

namespace OwowAgency\LaravelNotifications\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OwowAgency\LaravelNotifications\Resources\NotificationResource;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Paginate notifications that belongs to the notifiable.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginateForNotifiable($notifiable): JsonResponse
    {
        $notifiable = $this->getModelInstance($notifiable);

        $this->authorize('viewNotificationsOf', $notifiable);

        $notifications = $notifiable->notifications()->latest()->paginate();

        $resources = NotificationResource::collection($notifications);

        return new JsonResponse($resources);
    }

    /**
     * Get the model instance that should be binded to the route. This function
     * is needed because we can't bind model instance with dynamic model class
     * to a controller's method.
     * 
     * @param  string|\Illuminate\Database\Eloquent\Model  $value
     * @return \Illuminate\Database\Eloquent\Model
     * 
     * @throws \Exception
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function getModelInstance($value): Model
    {
        if ($value instanceof Model) {
            return $value;
        }

        // Get the model class via `model` key in the route's action array.
        $modelClass = request()->route()->getAction('model');

        if (! $modelClass) {
            throw new \Exception('Route must specify model class.');
        }

        // Get the model instance by resolving the route binding.
        $modelInstance = (new $modelClass)->resolveRouteBinding($value);

        if (! $modelInstance) {
            throw new ModelNotFoundException($modelInstance, $value);
        }

        return $modelInstance;
    }
}
