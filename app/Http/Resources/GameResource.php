<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            // 'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'status' => 'active',
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
