<?php

namespace OwowAgency\LaravelSettings\Controllers;

use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * The JSON resource to be used to serialize settings.
     *
     * @var string
     */
    private string $settingResource;

    public function __construct()
    {
        $this->settingResource = config('settings.resources.setting');
    }

    /**
     * Index settings that belongs to the model.
     *
     * @param  string|\Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexForModel($model): JsonResponse
    {
        $model = $this->getHasSettingsInstance($model);

        $this->authorize('viewSettingsOf', $model);

        $settings = $model->settings()->get();

        $resources = $this->settingResource::collection($settings);

        return new JsonResponse($resources);
    }
}
