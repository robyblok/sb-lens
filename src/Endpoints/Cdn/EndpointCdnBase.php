<?php

namespace StoryblokLens\Endpoints\Cdn;

abstract class EndpointCdnBase
{
    public function __construct(protected ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientCdn) {}


    abstract protected function endpoint(): string;

    abstract protected function method(): string;

    abstract protected function options(): array;

    public function getResponse()
    {

        return $this->clientCdn->request(
            $this->method(),
            $this->endpoint(),
            $this->options(),
        );

    }

    public function get(): array
    {
        return $this->getResponse()->toArray();
    }
}
