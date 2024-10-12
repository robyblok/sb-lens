<?php

namespace StoryblokLens\Endpoints;

class Me extends EndpointBase
{
    protected function endpoint(): string
    {
        return "v1/users/me";
    }

    protected function method(): string
    {
        return "GET";
    }

    protected function options(): array
    {
        return [];

    }

    public function id(): string
    {
        $user = $this->get();
        return $user->get("user.id", "");
    }



}
