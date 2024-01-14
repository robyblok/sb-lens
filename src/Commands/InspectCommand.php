<?php

namespace StoryblokLens\Commands;

use StoryblokLens\Resultset;
use StoryblokLens\SbClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InspectCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('inspect')

            ->addOption(
                'space',
                's',
                InputOption::VALUE_OPTIONAL,
                'The Space ID',
                ''
            )
            ->setDescription('Inspect some Storyblok space configuration.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $spaceId = $input->getOption("space");

        $io->title("Storyblok Lens");
        $client = SbClient::make();
        /*
        $response = $this->clientCdn->request(
            'GET',
            'v2/cdn/spaces/me',
            [
                'query' => [
                    'token' => $_ENV["STORYBLOK_ACCESS_TOKEN"]
                ],
            ]
        );
        $content = $response->toArray();
        */


        $response = $client->mapi()->request(
            'GET',
            'v1/spaces/' . $spaceId
        );
        $content = $response->toArray();
        file_put_contents(
            "./space_" . $spaceId . ".json",
            json_encode($response->toArray(), JSON_PRETTY_PRINT)
        );
        $space = $content["space"];
        $io->section('Inspecting Space (' . $spaceId . '): ' . $space["name"]);
        //$io->writeln('<comment>Space name</comment>: ' . $space["name"]);
        $resultSpace = Resultset::make($space);
        $resultSpace->add("name");
        $resultSpace->add("stories_count");
        $resultSpace->add("assets_count");
        $resultSpace->add("region");
        $resultSpace->printResult($io, "Info", "Value");

        $io->section("Limits");
        $resultSpace->reset($space["limits"]);
        $resultSpace->add("plan_level");
        $resultSpace->addByte("traffic_limit");
        $resultSpace->add("max_collaborators");
        $resultSpace->addOthers();
        $resultSpace->printResult($io, "Feature", "Limit");
        $resultSpace->printTable(
            $space["environments"],
            $io,
            ["name", "location"],
            ['Environment', 'URL']
        );
        $resultSpace->printTable(
            $space["options"]["languages"],
            $io,
            ["code", "name"],
            ['Lang Code', 'Language']
        );


        $response = $client->mapi()->request(
            'GET',
            "v1/apps/",
            [
                'query' => [
                    'space_id' => $spaceId,
                    'type' => 'installed'
                ],
            ]
        );
        $content = $response->toArray();
        $resultSpace->printTable(
            $content["apps"],
            $io,
            ["name", "intro"],
            ['Installed App', 'Info']
        );


        $response = $client->mapi()->request(
            'GET',
            sprintf('v1/spaces/%s/statistics', $spaceId),
            [
                'query' => [
                    'version' => 'new'
                ],
            ]
        );
        $content = $response->toArray();
        $resultSpace->reset($content);
        $resultSpace->add("collaborators_count");
        $resultSpace->add("all_stories_count");
        $resultSpace->add("all_assets_count");
        $resultSpace->add("all_components_count");
        $resultSpace->printResult($io, "Metric", "Count");

        return Command::SUCCESS;
    }



}
