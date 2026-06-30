<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConvertRequest;
use App\Services\Convert\ConvertService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConvertController extends Controller
{
    public function __invoke(ConvertRequest $request, ConvertService $convertService): JsonResponse|BinaryFileResponse
    {
        $data = $request->validated();

        return $convertService->convert($data);
    }

}
