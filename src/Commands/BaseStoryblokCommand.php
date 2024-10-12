<?php

namespace StoryblokLens\Commands;

use StoryblokLens\Libs\SbLensUtils;
use Symfony\Component\Console\Command\Command;
use StoryblokLens\Traits\StoryblokTrait;

abstract class BaseStoryblokCommand extends Command
{
    use StoryblokTrait;


    protected function initializeTwig()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../resources/views');
        $twig = new \Twig\Environment(
            $loader,
            /*[
                'cache' => __DIR__ . '/../../cache',
            ]*/
        );

        $function = new \Twig\TwigFilter(
            'to_bytes',
            fn($value): string => SbLensUtils::formatBytes($value),
        );
        $twig->addFilter($function);
        $function = new \Twig\TwigFilter(
            'plan_description',
            fn($value): string => $this->getPlanDescription($value),
        );
        $twig->addFilter($function);
        return $twig;

    }

}
