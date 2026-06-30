<?php

namespace App\Services\Convert;

use thiagoalessio\TesseractOCR\TesseractOCR;

class HocrConvert implements ConvertInterface
{

    #[\Override] public function generate(TesseractOCR $tesseract, string $absoluteOutputPath): string
    {
        $absoluteOutputPath = $absoluteOutputPath . '.hocr';
        $tesseract->setOutputFile($absoluteOutputPath);
        $tesseract->format('hocr');
        $tesseract->run();

        return $absoluteOutputPath;
    }
}
