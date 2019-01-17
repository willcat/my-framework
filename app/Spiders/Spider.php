<?php 
namespace App\Spiders;

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use React\Filesystem\FilesystemInterface;
use Symfony\Component\DomCrawler\Crawler;

class Spider
{
    private $client;
    private $filesystem;
    private $directory;

    public function __construct(Browser $browser, 
                                FilesystemInterface $filesystem, 
                                string $directory)
    {
        $this->client = $browser;
        $this->filesystem = $filesystem;
        $this->directory = $directory;
    }

    public function scrape($urls)
    {
        foreach ($urls as $url) {
            $this->client->get($url)->then(
                function(ResponseInterface $response) {
                    $this->processResponse((string) $response->getBody());
                }
            );
        }
    }

    private function processResponse(string $html)
    {
        $crawler = new Crawler($html);
        $imageUrl = $crawler->filter('.image-section__image')->attr('src');
        preg_match('/photos\/\d+\/([\w-\.]+)\?/', $imageUrl, $matches); // $matches[1] 包含一个文件名
        $filePath = $this->directory . DIRECTORY_SEPARATOR . $matches[1];
        $this->clent->get($imageUrl)->then(
            function(ResponseInterface $response) use ($filePath) {
                $this->filesystem->file($filePath)->putContents((string)$response->getBody());
            }
        );
    }
}