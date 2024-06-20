<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "message" => "Pill data retrieved successfully",
            'id' => $this->id,
            'name' => $this->name,
            'photo' =>asset($this->photo),
            'description' => $this->description,
            'dosages' => $this->dosages,
            'sideEffects' => $this->sideEffects,
            'contraindiacations' => $this->contraindiacations,
        ];
    }
}