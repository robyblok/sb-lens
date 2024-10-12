<?php

namespace StoryblokLens\Endpoints;

class Space extends EndpointBase
{
    protected function endpoint(): string
    {
        return 'v1/spaces/' . $this->spaceId;
    }

    protected function method(): string
    {
        return "DELETE";
    }

    protected function options(): array
    {
        return [];
    }
}
