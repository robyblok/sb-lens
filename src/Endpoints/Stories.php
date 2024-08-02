<?php

namespace StoryblokLens\Endpoints;

class Stories extends EndpointBase
{
    protected $filterOnlyFolders = false;

    protected $filterOnlyStories = false;

    protected $filterParentId = false;

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
        $query = [
            'with_summary' => 1,

            'page' => 1,
            'per_page' => 100
        ];
        if ($this->filterOnlyStories) {
            $query["story_only"] = true;
        }

        if ($this->filterOnlyFolders) {
            $query["folder_only"] = true;
        }

        if ($this->filterParentId !== false) {
            $query["with_parent"] = $this->filterParentId;
        }

        return [
            'query' => $query,
        ];
    }

    public function parentId($parentId): self
    {
        $this->filterParentId = $parentId;
        return $this;
    }

    public function onlyFolder($value = true): self
    {
        $this->filterOnlyFolders = $value;
        return $this;
    }

    public function onlyStory($value = true): self
    {
        $this->filterOnlyStories = $value;
        return $this;
    }

}
