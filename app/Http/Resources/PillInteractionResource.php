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
            'pill1' => [
                'id' => $this->pill1->id,
                'name' => $this->pill1->name,
                'photo' => asset($this->pill1->photo),
                'description' => $this->pill1->description,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,

            ],
            'pill2' => [
                'id' => $this->pill2->id,
                'name' => $this->pill2->name,
                'photo' => asset($this->pill2->photo),
                'description' => $this->pill2->description,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
        ];
    }
}
