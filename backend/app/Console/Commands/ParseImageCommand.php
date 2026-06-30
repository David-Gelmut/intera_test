<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

#[Signature('parse-image')]
#[Description('Command description')]
class ParseImageCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Путь к исходному изображению
        $imagePath = storage_path('app/public/test1.png');

        // 2. Имя и временный путь для будущего PDF
        $outputBaseName = 'result_' . time().'.pdf'; // Tesseract сам добавит расширение .pdf
        $tempOutputPath = storage_path('app/' . $outputBaseName);

        try {
            // 3. Запускаем распознавание с генерацией PDF

            $tes = new TesseractOCR($imagePath);
            dd($tes->availableLanguages());
            $tes->format('pdf');
            $tes->setOutputFile($tempOutputPath);
            $tes->run();

          /* $text = (new TesseractOCR($imagePath))
                ->lang('rus','deu')
                ->format('txt') // Указываем формат PDF
                ->custom('out', $tempOutputPath) // Указываем, куда сохранить результат
                ->run();*/




          // Storage::put($outputBaseName, $text);
            // Полный путь к созданному файлу
            $finalPdfPath = $tempOutputPath . '.pdf';

            // 4. Проверяем, что файл создался
           // if (file_exists($finalPdfPath)) {

                // Вариант А: Просто оставить файл в папке storage/app
                // Файл уже лежит там под именем $outputBaseName . '.pdf'

                // Вариант Б: Отдать файл пользователю на скачивание в браузер и удалить временный
              //  return response()->download($finalPdfPath)->deleteFileAfterSend(true);
           // }

            throw new \Exception("Файл PDF не был создан Tesseract.");

        } catch (\Exception $e) {
           /* return response()->json([
                'status' => 'error',
                'message' => 'Ошибка создания PDF: ' . $e->getMessage()
            ], 500);*/
        }


    }
}
