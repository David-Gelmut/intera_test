<?php

namespace App\Services\Convert;

use thiagoalessio\TesseractOCR\TesseractOCR;

class TsvConvert implements ConvertInterface
{

    #[\Override] public function generate(TesseractOCR $tesseract, string $absoluteOutputPath): string
    {
        $absoluteOutputPath = $absoluteOutputPath . '.xml';
        $tesseract->setOutputFile($absoluteOutputPath);
        $tesseract->format('xml');
        $tesseract->run();

        return $absoluteOutputPath;
    }
}
