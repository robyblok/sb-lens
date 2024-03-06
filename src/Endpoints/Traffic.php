<?php

namespace StoryblokLens\Endpoints;

class Traffic extends EndpointBase
{
    protected function endpoint(): string
    {
        return sprintf('v1/spaces/%s/statistics/all_traffic', $this->spaceId);
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return [
            'query' => [
                'start_date' => date('Y-m-d', strtotime('-5 days')),
                'end_date' => date('Y-m-d'),
                'group_by' => 'day'
            ],
        ];
    }
}
