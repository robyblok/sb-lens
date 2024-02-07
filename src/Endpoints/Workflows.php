<?php

namespace StoryblokLens\Endpoints;

class Workflows extends EndpointBase
{
    protected function endpoint(): string
    {
        return "v1/spaces/" . $this->spaceId . "/workflows";
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return             [
            'query' => [
                //'by_content_type' => "page",
                'include_stages' => true,
                'page' => 1,
                'per_page' => 100
            ],
        ];
    }
}
