<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PillInteractionResource extends JsonResource
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
            "id" => $this->id,
            'interaction_type' => $this->interaction_type,
            'interaction_description' => $this->interaction_description,
            'guides' => $this->guides,
            'pill1' => $this->pill1,
            'pill2' => $this->pill2
        ];
    }
}