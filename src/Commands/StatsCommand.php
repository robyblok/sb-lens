<?php

namespace StoryblokLens\Commands;

use StoryblokLens\SbClient;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends BaseStoryblokCommand
{
    protected function configure()
    {
        $this
            ->setName('stats')
            ->addOption(
                'space',
                's',
                InputOption::VALUE_OPTIONAL,
                'The Space ID',
                '',
            )
            ->setDescription('Retrieve statistics from a Space.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $spaceId = $input->getOption("space");

        $twig = $this->initializeTwig();

        $region = $this->getRegionFromSpaceId($spaceId);
        $client = SbClient::make($region);
        $statistics = $client
            ->statistics()
            ->spaceId($spaceId)
            ->get();
        $traffic = $client
            ->traffic()
            ->spaceId($spaceId)
            ->get();
        $assetsTraffic = $client
            ->assetsTraffic()
            ->spaceId($spaceId)
            ->get();

        //$output->writeln($statistics->toJson());
        //$output->writeln($traffic->toJson());
        //$output->writeln($assetsTraffic->toJson());

        $template = $twig->load('traffic.md');
        $output->writeln("");
        $output->write(
            $template->render([
                'traffic' => $traffic,
                'statistics' => $statistics,
            ]),
        );

        $template = $twig->load('stats/traffic-assets.md');
        $output->writeln("");
        $output->write(
            $template->render([
                'assetsTraffic' => $assetsTraffic->getBlock("assets"),
            ]),
        );
        exit();
    }



}
