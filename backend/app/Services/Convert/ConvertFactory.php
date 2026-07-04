<?php

namespace App\Services\Convert;

class ConvertFactory
{
    public static function make(string $format): ConvertInterface
    {
        return match ($format) {
            'txt' => new TxtConvert(),
            'pdf' => new PdfConvert(),
            'hocr' => new HocrConvert(),
            'tsv' => new TsvConvert(),
            default => throw new \InvalidArgumentException("Неподдерживаемый формат: {$format}"),
        };
    }
}
