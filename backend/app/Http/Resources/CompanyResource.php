<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'name' => $this->name ?? 'Синхронизация данных...',
            'yandex_url' => $this->yandex_url,
            'yandex_id' => $this->yandex_id,
            'rating' => (float) $this->rating,
            'rating_count' => (int) $this->ratings_count, // точное число оценок отдельно
            'review_count' => (int) $this->reviews_count, // точное число отзывов отдельно
            'updated_at' => $this->updated_at,
        ];
    }
}
