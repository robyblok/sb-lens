<?php

namespace StoryblokLens\Endpoints;

class Presets extends EndpointBase
{
    protected function endpoint(): string
    {
        return sprintf('v1/spaces/%s/presets', $this->spaceId);
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
