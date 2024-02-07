<?php

namespace StoryblokLens;

class SbChecker
{
    private array $defaultChecks = [];

    public function __construct()
    {
        $this->defaultChecks = $this->loadDefaultCheckers();
    }

    public static function make(): self
    {
        return new self();
    }

    private function loadDefaultCheckers(): array
    {
        return
            [
                "workflow" => [
                    "count" => 1,
                    "workflow_stages_count" => 3,

                ]
            ];
    }

    public function workflowsCheck($workflows, array $limits): array
    {
        $suggestions = [];
        $default = $this->defaultChecks["workflow"];
        if (count($workflows) === $default["count"] && $limits["max_custom_workflows"] > 0) {
            $suggestions[] = "I see " . count($workflows) . "workflows. You can set " . $limits["max_custom_workflows"] . " custom workflows";
        }

        return $suggestions;
    }

}
