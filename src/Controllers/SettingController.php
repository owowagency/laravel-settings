<?php

namespace OwowAgency\LaravelSettings\Controllers;

use Illuminate\Http\JsonResponse;
use OwowAgency\LaravelSettings\Support\SettingManager;

class SettingController extends Controller
{
    /**
     * The class name of the JSON resource to be used to serialize settings.
     *
     * @var string
     */
    private $settingResource;

    /**
     * The SettingController constructor.
     */
    public function __construct()
    {
        $this->settingResource = config('laravel-settings.resources.setting');
    }

    /**
     * Index settings that belongs to the model.
     *
     * @param  string|int|\OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface  $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexForModel($model): JsonResponse
    {
        $model = $this->getHasSettingsInstance($model);

        $this->authorize('viewSettingsOf', $model);

        $settings = SettingManager::getForModel($model);

        $resources = $this->settingResource::collection($settings);

        return new JsonResponse($resources);
    }
}
