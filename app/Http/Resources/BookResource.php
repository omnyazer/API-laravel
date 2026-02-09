<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'author' => strtoupper($this->author),
            'summary' => $this->summary,
            'isbn' => $this->isbn,
        ];
    }
}
