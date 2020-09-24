<?php

namespace OwowAgency\LaravelNotifications\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OwowAgency\LaravelNotifications\Resources\NotificationResource;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Paginate all notifications.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginate(): JsonResponse
    {
        $this->authorize('viewAny', DatabaseNotification::class);

        $notifications = DatabaseNotification::latest()->simplePaginate();

        return $this->createPaginatedResponse($notifications);
    }

    /**
     * Paginate notifications that belongs to the notifiable.
     *
     * @param  string|\Illuminate\Database\Eloquent\Model $notifiable
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginateForNotifiable($notifiable): JsonResponse
    {
        $notifiable = $this->getModelInstance($notifiable);

        $this->authorize('viewNotificationsOf', $notifiable);

        $notifications = $notifiable->notifications()->latest()->simplePaginate();

        return $this->createPaginatedResponse($notifications);
    }

    /**
     * Count notifications that belongs to the notifiable.
     *
     * @param  string|\Illuminate\Database\Eloquent\Model $notifiable
     * @return \Illuminate\Http\JsonResponse
     */
    public function countForNotifiable($notifiable): JsonResponse
    {
        $notifiable = $this->getModelInstance($notifiable);

        $this->authorize('viewNotificationsCountOf', $notifiable);

        // Get the count of read, unread, and all notifications.
        $notifsArray = $notifiable
            ->notifications()
            ->selectRaw('IF(read_at IS NULL, "unread", "read") as status')
            ->selectRaw('COUNT(*) as count')
            ->groupByRaw('status WITH ROLLUP')
            ->reorder()
            ->get()
            ->toArray();

        // Map the results to a flat associative array.
        foreach ($notifsArray as $notifs) {
            $status = $notifs['status'] ?? 'all';

            $data[$status] = $notifs['count'];
        }

        return new JsonResponse($data);
    }

    /**
     * Helper Methods
     * ========================================================================
     */

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

    /**
     * Create a paginated JSON response from the given paginator and resource class.
     *
     * @param  \Illuminate\Pagination\AbstractPaginator  $paginator
     * @param  string  $resourceClass
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createPaginatedResponse(
        AbstractPaginator $paginator,
        string $resourceClass = NotificationResource::class
    ): JsonResponse
    {
        $resources = $resourceClass::collection($paginator);

        $paginator = $paginator->setCollection($resources->collection);

        return new JsonResponse($paginator);
    }
}
