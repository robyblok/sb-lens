<?php

namespace StoryblokLens\Commands;

use StoryblokLens\Reporter;
use StoryblokLens\Resultset;


use StoryblokLens\SbClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


use function StoryblokLens\Termwind\{hint, title, subtitle, hr,  twoColumnItem, twoColumnList};

class SpacesCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('spaces')

            ->addOption(
                'region',
                'r',
                InputOption::VALUE_OPTIONAL,
                'The region',
                'EU',
            )

            ->setDescription('Retrieve the Storyblok Spaces.');
    }

    public static function getPlanDescription($planLevel): string
    {
        return match ($planLevel) {
            0 => 'Starter (Trial)',
            2 => 'Pro Space',
            1 => 'Standard Space',
            1000 => 'Development',
            100 => 'Community',
            200 => 'Entry',
            300 => 'Teams',
            301 => 'Business',
            400 => 'Enterprise',
            500 => 'Enterprise Plus',
            501 => 'Enterprise Essentials',
            502 => 'Enterprise Scale',
            503 => 'Enterprise Ultimate',

            default => $planLevel,
        };

    }

    private function initializeTwig()
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../resources/views');
        $twig = new \Twig\Environment($loader,
        /*[
            'cache' => __DIR__ . '/../../cache',
        ]*/);

        $function = new \Twig\TwigFilter('to_bytes',
            fn ($value) => Resultset::formatBytes($value)
        );
        $twig->addFilter($function);
        $function = new \Twig\TwigFilter(
            'plan_description',
            fn ($value) => self::getPlanDescription($value)
        );
        $twig->addFilter($function);
        return $twig;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $region = $input->getOption("region");
        title("Storyblok Lens - SPACES");
        $client = SbClient::make($region);
        $spaces = $client->space()->get()->getBlock("spaces");
        //$outputString = $spaces->toJson();
        //$output->write($outputString);
        subtitle('Found ' . $spaces->count() . ' spaces in ' . $region . ' region');
        foreach ($spaces as $space) {
            twoColumnItem($space->get("name"), $space->get("id"));
        }
        //View::make('report', ['name' => 'James']);
        subtitle('Found ' . $spaces->count() . ' spaces in ' . $region . ' region');


        exit();
    }



}
