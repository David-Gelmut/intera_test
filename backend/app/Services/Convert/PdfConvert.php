<?php

namespace App\Services\Convert;

use thiagoalessio\TesseractOCR\TesseractOCR;

class PdfConvert implements ConvertInterface
{

    #[\Override] public function generate(TesseractOCR $tesseract, string $absoluteOutputPath): string
    {
        $absoluteOutputPath = $absoluteOutputPath . '.pdf';
        $tesseract->setOutputFile($absoluteOutputPath);
        $tesseract->format('pdf');
        $tesseract->run();

        return $absoluteOutputPath;
    }
}
