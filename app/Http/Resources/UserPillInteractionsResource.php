<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPillInteractionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "pill_1_photo" => asset($this->pill1->photo),
            "pill_2_photo" => asset($this->pill2->photo),
            "interaction_type" => $this->PillInteraction->interaction_type,
            "interaction_id" => $this->PillInteraction->id,
            "created_at" => Carbon::parse($this->created_at)->diffForHumans()

        ];
    }
}
