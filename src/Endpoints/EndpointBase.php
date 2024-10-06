<?php

namespace StoryblokLens\Endpoints;

use HiFolks\DataType\Block;

abstract class EndpointBase
{
    protected $spaceId;

    public function __construct(protected ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientMapi) {}

    public function spaceId($spaceId): self
    {
        $this->spaceId = $spaceId;
        return $this;
    }

    abstract protected function endpoint(): string;

    abstract protected function method(): string;

    abstract protected function options(): array;

    public function getResponse()
    {
        return $this->clientMapi->request(
            $this->method(),
            $this->endpoint(),
            $this->options(),
        );
    }

    public function get(): Block
    {
        return Block::make($this->getResponse()->toArray());
    }
}
