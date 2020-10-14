<?php

namespace OwowAgency\LaravelSettings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OwowAgency\LaravelSettings\Support\SettingManager;

class SettingResource extends JsonResource
{
    /**
     * Transforms the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'title' => $this->resource['title'],
            'description' => $this->resource['description'],
            'type' => $this->resource['type'],
            'default' => $this->resource['default'],
            'key' => $this->resource['key'],
            'value' => SettingManager::convertToType(
                $this->resource['type'],
                $this->resource['value'],
            ),
        ];
    }
}
