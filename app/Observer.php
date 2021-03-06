<?php

namespace App;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlObservers\CrawlObserver as SpatieCrawlObserver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Manipulations;

class Observer extends SpatieCrawlObserver
{

    public $pages =[];

    /**
     * @param \Psr\Http\Message\UriInterface $url
     */
    public function willCrawl(UriInterface $url): void
    {
        if (in_array(__FUNCTION__, config('crawler-toolkit.log'))) {
            Log::debug('Crawler: ' . $url . ' will be crawled.');
        }
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ): void {
        $page = new \App\Models\Page();
        $crawler = new DomCrawler($response->getBody()->__toString());

        $page['url'] = $url->__toString();
        $page['hostname'] = $url->getHost();
        $page['url_path'] = $url->getPath();
        $crawler->filterXPath('//title')->each(function (DomCrawler $node, $i) use ($page) {
            $page['title'] = $node->text();
        });
        $page['status'] = $response->getStatusCode();
        $page['description'] = $response->getReasonPhrase();
        // $page['body'] = $response->getBody()->__toString();
        $page['date_http_header'] = $response->getHeaderLine('date');

        try {
            if ($page['status'] == 200) {
                $file_name = $page['hostname'] . '/' . $page['url_path'];
                $file_name = str_replace('/', '_', $file_name);
                $file_name = str_replace('.', '-', $file_name) . '.png';
                $path_name = storage_path('/app/public/' . $file_name);

                Browsershot::url($url)
                    ->noSandbox()
                    ->windowSize(1920, 1080)
                    ->fit(Manipulations::FIT_CONTAIN, 640, 480)
                    ->save($path_name);

                $page['image_path'] = Storage::url($file_name);
            }
        } catch (Throwable $e) {}

        array_push($this->pages, $page);

        if (in_array(__FUNCTION__, config('crawler-toolkit.log'))) {
            Log::debug(
                'Crawler: ' . $url . ' crawled' .
                (($foundOnUrl === null) ? '.' : ', found on ' . $foundOnUrl)
            );
        }
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ): void {
        if (in_array(__FUNCTION__, config('crawler-toolkit.log'))) {
            Log::debug('Crawler: ' . $url . ' failed: ' . $requestException->getMessage());
        }
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        if (in_array(__FUNCTION__, config('crawler-toolkit.log'))) {
            Log::debug('Crawler: Finished');
        }
    }
    
}