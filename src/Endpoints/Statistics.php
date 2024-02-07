<?php

namespace StoryblokLens\Endpoints;

class Statistics extends EndpointBase
{
    protected function endpoint(): string
    {
        return sprintf('v1/spaces/%s/statistics', $this->spaceId);
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return [
            'query' => [
                'version' => 'new'
            ],
        ];
    }
}
