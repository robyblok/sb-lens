<?php

namespace StoryblokLens\Endpoints;

class Stories extends EndpointBase
{
    protected function endpoint(): string
    {
        return "v1/spaces/" . $this->spaceId . "/stories";
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return [
            'query' => [
                'with_summary' => 1,
                'story_only' => true,
                'page' => 1,
                'per_page' => 100
            ],
        ];
    }
}
