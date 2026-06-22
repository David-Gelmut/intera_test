<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'company_id' => $this->company_id,
            'author_name' => $this->yandex_author_name,
            'author_id' => $this->yandex_author_id,
            'text' => $this->text,
            'rating' => (float) $this->rating,
            'published_at' => $this->published_at,
        ];
    }

   /* public function withResponse(Request $request, \Illuminate\Http\JsonResponse $response): void
    {
        // Получаем исходные данные, которые сгенерировал пагинатор Laravel
        $responseData = $response->getData(true);

        if (isset($responseData['meta'])) {
            $response->setData([
                'data' => $responseData['data'],
                'meta' => [
                    'current_page' => $responseData['meta']['current_page'],
                    'last_page'    => $responseData['meta']['last_page'],
                    'per_page'     => $responseData['meta']['per_page'],
                    'total'        => $responseData['meta']['total'],
                ]
            ]);
        }
    }*/

    /*public function withResponse(Request $request, \Illuminate\Http\JsonResponse $response): void
    {
        $jsonResponse = json_decode($response->getContent(), true);

        // Пересобираем структуру ответа по вашему вкусу
        $customResponse = [
            'result' => $jsonResponse['data'],
            'pagination' => [
                'total_records' => $jsonResponse['meta']['total'] ?? 0,
                'current' => $jsonResponse['meta']['current_page'] ?? 1,
                'limit' => $jsonResponse['meta']['per_page'] ?? 10,
            ]
        ];

        $response->setData($customResponse);
    }*/
}
