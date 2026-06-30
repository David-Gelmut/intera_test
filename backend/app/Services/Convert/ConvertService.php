<?php

namespace App\Services\Convert;


use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ConvertService
{
    /**
     *  Метод конвертации
     */
    public function convert(array $data): JsonResponse|BinaryFileResponse
    {
        $image = $data['image'];
        $format = $data['format'];

        $absoluteImagePath = $this->storeImageInStorage($image);
        $absoluteOutputPath = storage_path('app/private/tmp/result_' . time());

        try {

            $finalFile = $this->convertTesseractOCR($absoluteImagePath, $absoluteOutputPath, $format);

            if (file_exists($finalFile)) {
                @unlink($absoluteImagePath);
                return response()->download($finalFile)->deleteFileAfterSend(true);
            }

            throw new \Exception("Не удалось сгенерировать файл ответа.");

        } catch (\Exception $e) {
            @unlink($absoluteImagePath);
            if (isset($finalFile)) @unlink($finalFile);

            return response()->json(['error' => 'Ошибка OCR: ' . $e->getMessage()], 500);
        }
    }

    /**
     *  Сохраняет входящее изображение в storage/app/private/tmp и возвращает абсолютный путь до него.
     */
    private function storeImageInStorage(mixed $image): string
    {
        $imageName = 'convert_' . time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('tmp', $imageName, 'local');
        return storage_path('app/private/' . $imagePath);
    }

    /**
     *  Tesseract отрабатывает и возвращает путь к сохраненному файлу.
     */
    private function convertTesseractOCR(string $absoluteImagePath, string $absoluteOutputPath, string $format): string
    {
        $tesseract = new TesseractOCR($absoluteImagePath);
        //$tesseract->availableLanguages();
        $tesseract->lang('rus', 'eng');

        $strategy = ConvertFactory::make($format);
        return $strategy->generate($tesseract, $absoluteOutputPath);
    }

}
