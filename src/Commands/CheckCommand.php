<?php

namespace StoryblokLens\Commands;

use StoryblokLens\Reporter;
use StoryblokLens\Resultset;


use StoryblokLens\SbClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//use function StoryblokLens\{hint, title, subtitle, hr,  twoColumnItem, twoColumnList};

class CheckCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('check')

            ->addOption(
                'space',
                's',
                InputOption::VALUE_OPTIONAL,
                'The Space ID',
                '',
            )

            ->setDescription('Check the Storyblok space usage.');
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

        $spaceId = $input->getOption("space");

        $twig = $this->initializeTwig();

        $region = ($spaceId > 1_000_000) ? "US": "EU";
        $client = SbClient::make($region);

        $content = $client->space()->spaceId($spaceId)->get();

        file_put_contents(
            "./space_" . $spaceId . "-" . time() . ".json",
            $content->toJson(),
        );
        $space = $content->getBlock("space");
        $statistics = $client
            ->statistics()
            ->spaceId($spaceId)
            ->get();
        $traffic = $client
            ->traffic()
            ->spaceId($spaceId)
            ->get();
        $components = $client
            ->components()
            ->spaceId($spaceId)
            ->get();
        $apps = $client->apps()->spaceId($spaceId)->get();

        $template = $twig->load('overview.md');

        $output->write(
            $template->render([
                'spaceId' => $spaceId,
                'space' => $space,
                'traffic' => $traffic,
                'statistics' => $statistics,
                'components' => $components->getBlock("components"),
                'apps' => $apps->getBlock("apps")
            ])
        );

        $template = $twig->load('traffic.md');
        $output->writeln("");
        $output->write(
            $template->render([
                'spaceId' => $spaceId,
                'space' => $space,
                'traffic' => $traffic,
                'statistics' => $statistics,
                'components' => $components
            ])
        );


        $branches = $client->branches()->spaceId($spaceId)->get();

        $appsBlock = $apps->getBlock("apps");
        $hasBackupApp = $appsBlock->where(
            'slug', 'backups'
        )->exists();
        $hasDimensionApp = $appsBlock->where(
            'slug',
            'locales'
        )->exists();
        $hasPipelineApp = $appsBlock->where(
            'slug',
            'branches'
        )->exists();
        $hasTasksApp = $appsBlock->where(
            'slug',
            'tasks'
        )->exists();
        $folders = $client->stories()
            ->onlyFolder()
            ->parentId(0)
            ->spaceId($spaceId)
            ->get();
        $template = $twig->load('project-structure.md');
        $output->writeln("");
        $output->write(
            $template->render([
                'spaceId' => $spaceId,
                'space' => $space,
                'folders' => $folders,
                'branches' => $branches

            ])
        );







        $components = $client
            ->components()
            ->spaceId($spaceId)
            ->get();


        $numContentTypes = 0;
        $numNestable = 0;
        $numUniversal = 0;
        $numWithPreset = 0;
        $numContentTypes = $components->getBlock("components")
            ->where("is_root")
            ->where("is_nestable", false)
        ->count();
        $numNestable = $components->getBlock("components")
        ->where("is_root", false)
        ->where("is_nestable")
        ->count();
        $numUniversal = $components->getBlock("components")
        ->where("is_root")
        ->where("is_nestable")
        ->count();
        $numWithPreset= array_sum($components->getBlock("components")->forEach(fn ($element) => count($element["all_presets"]))->toArray());
        $presets = $client
            ->presets()
            ->spaceId($spaceId)
            ->get();
        $numPresets = $presets->getBlock("presets")->count();
        $template = $twig->load('block-library.md');
        $output->writeln("");
        $output->write(
            $template->render([
                'spaceId' => $spaceId,
                'space' => $space,
                'components' => $components,
                'numContentTypes' => $numContentTypes,
                'numNestable' => $numNestable,
                'numUniversal' => $numUniversal,
                'numWithPreset' => $numWithPreset,
                'numPresets' => $numPresets

            ])
        );



        //$output->write($r->getString());

        //View::make('report', ['name' => 'James']);



        exit();
    }



}
