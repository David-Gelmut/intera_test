<?php

namespace App\Services\Convert;

use thiagoalessio\TesseractOCR\TesseractOCR;

interface ConvertInterface
{
    /**
     * Обрабатывает изображение и возвращает абсолютный путь к готовому файлу.
     */
    public function generate(TesseractOCR $tesseract, string $absoluteOutputPath): string;
}
