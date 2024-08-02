<?php

namespace StoryblokLens\Endpoints;

class Branches extends EndpointBase
{
    protected function endpoint(): string
    {
        return 'v1/spaces/' . $this->spaceId . '/branches/';
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return [];
    }
}
