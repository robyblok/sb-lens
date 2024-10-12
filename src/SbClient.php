<?php

namespace StoryblokLens;

use Dotenv\Dotenv;
use StoryblokLens\Endpoints\Apps;
use StoryblokLens\Endpoints\Branches;
use StoryblokLens\Endpoints\Cdn\CdnStories;
use StoryblokLens\Endpoints\Components;
use StoryblokLens\Endpoints\Me;
use StoryblokLens\Endpoints\Presets;
use StoryblokLens\Endpoints\Space;
use StoryblokLens\Endpoints\Statistics;
use StoryblokLens\Endpoints\Statistics\AssetsTraffic;
use StoryblokLens\Endpoints\Statistics\Traffic;
use StoryblokLens\Endpoints\Stories;
use StoryblokLens\Endpoints\Story;
use StoryblokLens\Endpoints\Workflows;
use StoryblokLens\Traits\StoryblokTrait;
use Symfony\Component\HttpClient\HttpClient;

class SbClient
{
    use StoryblokTrait;

    private ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientMapi = null;

    private ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientOauth = null;


    private ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientCdn = null;

    private $personalAccessToken = "";

    public static function make(string $region = "EU"): self
    {
        $client = new self();
        $client->init($region);
        return $client;
    }

    public function init(string $region = "EU"): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        $this->personalAccessToken = $_ENV["STORYBLOK_OAUTH_TOKEN"];

        $baseUriMapi = $this->baseUriFromRegionForMapi($region);

        $this->clientMapi = HttpClient::create()
            ->withOptions([
                'base_uri' => $baseUriMapi,
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $this->personalAccessToken,
                    ],
            ]);

        $baseUriOauth = $this->baseUriFromRegionForOauth($region);
        $this->clientOauth = HttpClient::create()
            ->withOptions([
                'base_uri' => $baseUriOauth,
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $this->personalAccessToken,
                    ],
            ]);


        $this->clientCdn = HttpClient::create()
            ->withOptions([
                'base_uri' => 'https://api.storyblok.com/v2',
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
            ]);
    }

    public function mapi(): ?\Symfony\Contracts\HttpClient\HttpClientInterface
    {
        return $this->clientMapi;
    }

    public function cdn(): ?\Symfony\Contracts\HttpClient\HttpClientInterface
    {
        return $this->clientCdn;
    }

    public function story(): Story
    {
        return new Story($this->clientMapi);
    }

    public function space(): Space
    {
        return new Space($this->clientMapi);
    }

    public function apps(): Apps
    {
        return new Apps($this->clientMapi);
    }

    public function statistics(): Statistics
    {
        return new Statistics($this->clientMapi);
    }

    public function traffic(): Traffic
    {
        return new Traffic($this->clientMapi);
    }

    public function assetsTraffic(): AssetsTraffic
    {
        return new AssetsTraffic($this->clientMapi);
    }

    public function presets(): Presets
    {
        return new Presets($this->clientMapi);
    }

    public function workflows(): Workflows
    {
        return new Workflows($this->clientMapi);
    }

    public function stories(): Stories
    {
        return new Stories($this->clientMapi);
    }

    public function components(): Components
    {
        return new Components($this->clientMapi);
    }

    public function branches(): Branches
    {
        return new Branches($this->clientMapi);
    }

    public function me(): Me
    {
        return new Me($this->clientMapi);
    }

    public function cdnStories(): CdnStories
    {
        return new CdnStories($this->clientCdn);
    }


}
