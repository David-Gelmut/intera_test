<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;

class CompanyService
{
    public function getOrCreateByUrlAndId(string $url, string $id): Company
    {
        return Company::query()->firstOrCreate([
            'yandex_url' => $url,
            'yandex_id' => $id
        ]);
    }

    public function getByUrl(string $url): ?Company
    {
        return Company::query()
            ->where('yandex_url', $url)
            ->first();
    }

    public function getCompanyReviewsWithPaginate(Company $company): LengthAwarePaginator
    {
        return $company
            ->reviews()
            ->orderBy('published_at', 'desc')
            ->paginate(50);
    }

    public function sync(string $url): void
    {
        $exitCode = Artisan::call('parse', [
            'url' => $url
        ]);

        if ($exitCode !== 0) {
            throw new \Exception("Консольная команда 'parse' завершилась с ошибкой. Код: {$exitCode}");
        }
    }
}
