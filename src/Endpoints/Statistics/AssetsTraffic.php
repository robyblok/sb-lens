<?php

namespace StoryblokLens\Endpoints\Statistics;

class AssetsTraffic extends \StoryblokLens\Endpoints\EndpointBase
{
    protected function endpoint(): string
    {
        return sprintf('v1/spaces/%s/statistics/assets_traffic', $this->spaceId);
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return [
            'query' => [
                'start_date' => date('Y-m-d', strtotime('-7 days')),
                'end_date' => date('Y-m-d', strtotime('+1 day')),
                //'group_by' => 'day',
            ],
        ];
    }
}
