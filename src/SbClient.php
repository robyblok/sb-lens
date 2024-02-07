<?php

namespace StoryblokLens;

use Dotenv\Dotenv;
use StoryblokLens\Endpoints\Apps;
use StoryblokLens\Endpoints\Presets;
use StoryblokLens\Endpoints\Space;
use StoryblokLens\Endpoints\Statistics;
use StoryblokLens\Endpoints\Stories;
use StoryblokLens\Endpoints\Story;
use StoryblokLens\Endpoints\Workflows;
use Symfony\Component\HttpClient\HttpClient;

class SbClient
{
    private ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientMapi = null;

    private ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientCdn = null;

    private $personalAccessToken = "";

    public static function make(): self
    {
        $client = new self();
        $client->init();
        return $client;
    }

    public function init(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        $this->personalAccessToken = $_ENV["STORYBLOK_OAUTH_TOKEN"];
        //$this->spaceAccesToken = $_ENV["STORYBLOK_ACCESS_TOKEN"];

        $this->clientMapi = HttpClient::create()
            ->withOptions([
                'base_uri' => 'https://mapi.storyblok.com',
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $this->personalAccessToken
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


}
