<?php

namespace App\Http\Controllers\Parser;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Jobs\SyncYandexCompanyJob;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;


class CompanyController extends Controller
{
    public function __construct(private CompanyService $companyService)
    {
    }

    public function store(StoreCompanyRequest $companyRequest): JsonResponse
    {
        try {

            $url = $companyRequest->validated('url');

            $company = $this->companyService->getByUrl($url);

            if($company) {

                return response()->json([
                    'status' => 'success',
                    'company' => CompanyResource::make($company) ,
                    'reviews' => $this->companyService->getCompanyReviewsWithPaginate($company)
                ]);
            }

             //$this->companyService->sync($url);
            SyncYandexCompanyJob::dispatch($url);

            return response()->json([
                'status' => 'processing',
                'message' => 'Запрос принят. Синхронизация отзывов запущена в фоновом режиме.',
            ], 202);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Не удалось выполнить синхронизацию: ' . $e->getMessage()
            ], 500);
        }
    }

}
