<?php

namespace StoryblokLens\Endpoints;

class Apps extends EndpointBase
{
    protected function endpoint(): string
    {
        return "v1/apps/";
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return [
            'query' => [
                'space_id' => $this->spaceId,
                'type' => 'installed'
            ],
        ];
    }


}
