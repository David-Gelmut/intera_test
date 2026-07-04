<?php

namespace App\Services\Convert;

use thiagoalessio\TesseractOCR\TesseractOCR;

class BaseConvert
{

    protected TesseractOCR $tesseractOCR;

    public function __construct()
    {
        $this->tesseractOCR = new TesseractOCR();
    }
}
