<?php 
/*
|--------------------------------------------------------------------------
| buzz-react
|--------------------------------------------------------------------------
|
| buzz-react 是一个基于 ReactPHP 的简单、
| 致力于并发处理大量 HTTP 请求的异步 HTTP 客户端
|
*/
require __DIR__.'/vendor/autoload.php';

use Clue\React\Buzz\Browser;
use React\Filesystem\Filesystem;
use App\Spiders\Spider;

$loop = \React\EventLoop\Factory::create();

$scraper = new Spider(
    new Browser($loop), Filesystem::create($loop), __DIR__ . '/images'
);

$spider->scrape([
        'https://www.pexels.com/photo/adorable-animal-blur-cat-617278/'
    ]);

$loop->run();