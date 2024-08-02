<?php

namespace StoryblokLens\Endpoints\Cdn;

class CdnStories extends EndpointCdnBase
{
    protected function endpoint(): string
    {
        return "v2/cdn/stories";
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return [
            'query' => [
                'token' => $_ENV["STORYBLOK_ACCESS_TOKEN"],
                'level' => 1,
                'version' => 'draft',
            ],
        ];
    }
}
