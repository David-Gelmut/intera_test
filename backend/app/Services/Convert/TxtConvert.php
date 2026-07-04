<?php

namespace App\Services\Convert;

use thiagoalessio\TesseractOCR\TesseractOCR;

class TxtConvert implements ConvertInterface
{

    #[\Override] public function generate(TesseractOCR $tesseract, string $absoluteOutputPath): string
    {
        $absoluteOutputPath = $absoluteOutputPath . '.txt';
        $tesseract->setOutputFile($absoluteOutputPath);
        $tesseract->format('txt');
        $tesseract->run();

        return $absoluteOutputPath;
    }
}
