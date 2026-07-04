<?php

namespace App\Console\Commands;

use App\Services\CompanyService;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\NavigationExpired;
use HeadlessChromium\Exception\OperationTimedOut;
use HeadlessChromium\Page;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

#[Signature('parse {url}')]
#[Description('Command description')]
class ParseYandexMapsCommand extends Command
{
    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(CompanyService $companyService)
    {
        $yandexUrl = $this->argument('url');
        $yandexId = $this->extractCompanyId($yandexUrl);

        $company = $companyService->getOrCreateByUrlAndId($yandexUrl, $yandexId);

        $parsedData = $this->parse($yandexUrl);

        DB::transaction(function () use ($company, $parsedData, $yandexId) {

            $company->update([
                'yandex_id' => $yandexId,
                'name' => $parsedData['name'],
                'rating' => $parsedData['rating'],
                'ratings_count' => $parsedData['ratings_count'],
                'reviews_count' => $parsedData['reviews_count'],
            ]);

            foreach ($parsedData['reviews'] as $reviewData) {
                $company->reviews()->updateOrCreate(
                    ['yandex_author_id' => $reviewData['yandex_author_id']],
                    [
                        'yandex_author_name' => $reviewData['yandex_author_name'],
                        'text' => $reviewData['text'],
                        'rating' => $reviewData['rating'],
                        'published_at' => $reviewData['published_at'],
                        'review_hash' => Hash::make('review_hash'),
                    ]
                );
            }
        });
    }

    private function parse(string $yandexUrl): array
    {
        try {

            $browserFactory = new BrowserFactory();

            $browser = null;

            $browser = $browserFactory->createBrowser([

                'headless' => true,
                'sendSyncAndAsync'   => true,
                'noSandbox'       => true,
                'connectionDelay'    => 1.0,
                'customFlags'        => ['--disable-crash-reporter'],
                'windowSize' => ['1920', '1080'],
                'args' => [
                    '--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-gpu',
                    '--blink-settings=imagesEnabled=false',
                    '--no-zygote',
                    '--single-process',
                    '--disable-extensions',
                    '--disable-software-rasterizer',
                    '--remote-debugging-port=9222',
                    '--blink-features=AutomationControlled',

                ],
                //'executablePath' => '/usr/bin/google-chrome-stable',
                'executablePath' => env('CHROME_PATH', '/usr/bin/chromium-browser'),
            ]);

            // Открываем новую вкладку
            $page = $browser->createPage();

            // Формируем прямую ссылку на вкладку с отзывами
            $reviewsUrl = rtrim($yandexUrl, '/') . '/reviews/';

            // Переходим по URL и дожидаемся загрузки базового HTML
            $page->navigate($reviewsUrl)->waitForNavigation(Page::DOM_CONTENT_LOADED);
            $page->waitUntilContainsElement('.business-reviews-card-view__reviews-container'); // таймаут 30 сек

            // Имитируем прокрутку для подгрузки всех ~600 отзывов
            for ($i = 0; $i < 25; $i++) {

                $page->evaluate("
                    (function() {
                        // Ищем все карточки отзывов, которые отрисованы в данный момент
                        let reviews = document.querySelectorAll('.business-reviews-card-view__review');
                        if (reviews.length > 0) {
                            // Берем самую последнюю карточку в списке
                            let lastReview = reviews[reviews.length - 1];
                            // Принудительно скроллим контейнер к ней
                            lastReview.scrollIntoView({ block: 'end', behavior: 'auto' });
                        } else {
                            // Если карточки не нашлись (вдруг класс сменился), скроллим окно
                            window.scrollTo(0, document.body.scrollHeight);
                        }
                    })()
                ");

                // Делаем микропаузу (0.4 сек), чтобы Яндекс успевал подгружать данные по XHR
                usleep(400000);
            }

            return $this->parseYandexHtml($page->getHtml());

        } catch (OperationTimedOut|NavigationExpired|\Exception $e) {
            \Log::error('Ошибка парсинга: ' . $e->getMessage());
            throw new \Exception('Не удалось загрузить данные с Яндекс.Карт: ' . $e->getMessage());
        } finally {
            if ($browser !== null) {
                $browser->close();
            }
        }
    }

    function parseYandexHtml(string $htmlContent): array
    {
        $dom = new \DOMDocument();

        // Отключаем вывод предупреждений о тегах HTML5
        libxml_use_internal_errors(true);

        // Принудительно читаем строку в кодировке UTF-8, чтобы русский текст не превратился в иероглифы
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $htmlContent);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Имя
        $nameNode = $xpath->query(".//*[contains(@class, 'orgpage-header-view__header')]")->item(0);
        $name = $nameNode->textContent;

        //Средняя оценка
        $query = "(.//*[contains(@class, 'business-summary-rating-badge-view__rating-text')])[1] | (.//*[contains(@class, 'business-summary-rating-badge-view__rating-text')])[3]";

        $divs = $xpath->query($query);

        $firstDiv = $divs->item(0)->textContent; // Это первый div на странице
        $thirdDiv = $divs->item(1)->textContent; // Это третий div на странице (стал вторым в выборке)

        $average = (float)($firstDiv . '.' . $thirdDiv);

        //  Число оценок
        $countRatingsNode = $xpath->query("//*[contains(@class, 'business-rating-amount-view _summary')]")->item(0);
        $countRatings = $countRatingsNode->textContent;

        //  Число отзывов
        $countReviewsNode = $xpath->query("//*[contains(@class, 'card-section-header__title _wide')]")->item(0);
        $countReviews = $countReviewsNode->textContent;


        // Собираем массив отзывов
        $parsedReviews = [];

        // Находим все карточки отзывов. У Яндекса корневой класс одного отзыва — business-review-view
        $reviewNodes = $xpath->query("//*[contains(@class, 'business-reviews-card-view__review')]");

        foreach ($reviewNodes as $node) {

            // Автор отзыва
            $authorNode = $xpath->query(".//*[contains(@class, 'business-review-view__author-name')]", $node)->item(0);
            if ($authorNode) {
                $spanNode = $xpath->query(".//span", $authorNode)->item(0);
                $authorName = $spanNode ? trim($spanNode->textContent) : '';
            }

            // Текст отзыва
            $textNode = $xpath->query(".//*[contains(@class, 'spoiler-view__text-container')]", $node)->item(0);
            if ($textNode) {
                $text = trim($textNode->textContent);
            }

            // Рейтинг отзыва
            $ratingNote = $xpath->query(".//*[contains(@class, 'business-review-view__rating')]", $node)->item(0);
            $reviewRatingNode = $xpath->query(".//*[@itemprop='reviewRating']", $ratingNote)->item(0);

            if ($reviewRatingNode) {
                $ratingValueNode = $xpath->query(".//*[@itemprop='ratingValue']", $reviewRatingNode)->item(0);
                if ($ratingValueNode) {
                    $rating = $ratingValueNode->getAttribute('content');
                }
            }

            $dateNote = $xpath->query(".//*[contains(@class, 'business-review-view__date')]", $node)->item(0);
            $reviewDateNode = $xpath->query(".//*[@itemprop='datePublished']", $dateNote)->item(0);

            if ($reviewDateNode) {
                $date = $reviewDateNode->getAttribute('content');
            }

            // Уникальный идентификатор
            $uuidReviewUser = null;
            $uuidReviewUserNode = $xpath->query(".//*[contains(@class, 'business-review-view__link')]", $node)->item(0);

            if ($uuidReviewUserNode) {
                $uuidReviewUser = $this->parseLinkUserToUuid($uuidReviewUserNode->getAttribute('href'));
            }

            // Идентификатор для анонимов
            if (empty($uuidReviewUser)) {
                $uuidReviewUser = 'anon_' . md5($text . $date);
            }

            // Хэш отзыва для исключения дубликатов
            $reviewHash = md5($text . $date . $authorName);

            $parsedReviews[] = [
                'yandex_author_id' => $uuidReviewUser,
                'yandex_author_name' => $authorName ?? 'Аноним',
                'text' => $text ?? '',
                'rating' => $rating ?: 5,
                'published_at' => Carbon::parse($date)->toDateTimeString() ?? date('Y-m-d H:i:s'),
                'review_hash' => $reviewHash,
            ];
        }

        return [
            'name' => $name,
            'reviews_count' => $this->clearCount($countReviews),
            'ratings_count' => $this->clearCount($countRatings),
            'rating' => $average,
            'reviews' => $parsedReviews
        ];
    }

    private function clearCount(mixed $count): int
    {
        return (int)preg_replace('/[^0-9]/', '', $count);
    }

    private function parseLinkUserToUuid(string $link): string
    {
        $yandexAuthorId = '';
        if (preg_match('/\/user\/([a-z0-9]+)/i', $link, $matches)) {
            $yandexAuthorId = $matches[1];
        }
        return $yandexAuthorId;
    }

    /**
     * @throws \Exception
     */
    public function extractCompanyId(string $url): string
    {
        if (preg_match('/\/org\/([^\/]+\/)?(\d+)/', $url, $matches)) {
            return $matches[2];
        }

        throw new \Exception('Не удалось извлечь идентификатор организации из ссылки.');
    }

}
