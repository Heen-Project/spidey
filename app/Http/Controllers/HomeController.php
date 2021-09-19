<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Crawler\Crawler;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('app');
    }

    public function crawl(Request $request)
    {
        $url = $request->url;
        if ($request->reload) Cache::forget(md5($url));
        
        $observer = new \App\Observer;
        $queue = Cache::remember(md5($url), 3600, function() { return new \Spatie\Crawler\CrawlQueues\ArrayCrawlQueue; });

        Crawler::create()
            ->setCurrentCrawlLimit(5)
            ->setCrawlQueue($queue)
            ->setCrawlProfile(new \Spatie\Crawler\CrawlProfiles\CrawlSubdomains($url))
            ->addCrawlObserver($observer)
            ->startCrawling($url);
        Cache::put(md5($url), $queue, 3600);

        return response()->json($observer->pages, 200);
    }
}
