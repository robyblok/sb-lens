<?php

namespace StoryblokLens\Endpoints;

class Components extends EndpointBase
{
    protected function endpoint(): string
    {
        return sprintf('v1/spaces/%s/components', $this->spaceId);
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
