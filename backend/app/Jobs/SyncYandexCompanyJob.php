<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\CompanyService;
use App\Services\YandexParserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;

class SyncYandexCompanyJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private string $url)
    {
    }

    public function handle(): void
    {
        try {
            $command = sprintf(
                'php %s parse "%s"',
                base_path('artisan'),
                $this->url
            );

            $result = Process::timeout(300)->run($command);

            if ($result->failed()) {
                throw new \Exception($result->errorOutput());
            }

        } catch (\Exception $e) {
            \Log::error("Ошибка синхронизации" . $e->getMessage());
            throw $e;
        }
    }
}
