<?php

namespace OwowAgency\LaravelSettings\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    /**
     * Get the model instance that should be binded to the route. This function
     * is needed because we can't bind model instance with dynamic model class
     * to a controller's method.
     * 
     * @param  string|\Illuminate\Database\Eloquent\Model  $value
     * @param  string[]  $interfaces
     * @return \Illuminate\Database\Eloquent\Model
     * 
     * @throws \Exception
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function getModelInstance($value, array $interfaces = []): Model
    {
        if ($value instanceof Model) {
            $modelInstance = $value;
        } else {
            // Get the model class via `model` key in the route's action array.
            $modelClass = request()->route()->getAction('model');

            if (! $modelClass) {
                throw new \Exception('Route must specify model class.');
            }

            // Get the model instance by resolving the route binding.
            $modelInstance = (new $modelClass)->resolveRouteBinding($value);

            if (! $modelInstance) {
                throw (new ModelNotFoundException)->setModel($modelClass, $value);
            }
        }

        // Validate that the model instance implements the required interfaces.
        validate_interfaces_implemented($modelInstance, $interfaces);

        return $modelInstance;
    }

    /**
     * Get the HasSetting instance that should be binded to the route.
     * 
     * @param  string|\Illuminate\Database\Eloquent\Model  $value
     * @param  string[]  $interfaces
     * @return \OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface
     */
    protected function getHasSettingsInstance($value, array $interfaces = [HasSettingsInterface::class]): HasSettingsInterface
    {
        return $this->getModelInstance($value, $interfaces);
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
        string $resourceClass
    ): JsonResponse
    {
        $resources = $resourceClass::collection($paginator);

        $paginator = $paginator->setCollection($resources->collection);

        return new JsonResponse($paginator);
    }
}
