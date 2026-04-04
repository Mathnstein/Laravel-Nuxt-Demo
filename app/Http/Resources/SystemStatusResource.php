<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'            => $this['title'],
            'database_status'  => $this['database_status'],
            'database_version' => $this['database_version'],
            'redis_status'     => $this['redis_status'],
            'server_time'      => $this['server_time'],
            'php_version'      => $this['php_version'],
        ];
    }
}
