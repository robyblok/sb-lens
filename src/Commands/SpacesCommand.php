<?php

namespace StoryblokLens\Commands;

use StoryblokLens\SbClient;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function StoryblokLens\Termwind\{title, subtitle, twoColumnItem};

class SpacesCommand extends BaseStoryblokCommand
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


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $region = $input->getOption("region");
        title("Storyblok Lens - SPACES");
        $client = SbClient::make($region);

        $currentUserId = $client->me()->id();
        if ($currentUserId === "") {
            $output->write("Current user not found, please check your Personal Access Token");
            exit(-1);
        }

        $spaces = $client->space()->get()->getBlock("spaces");

        subtitle('Found ' . $spaces->count() . ' spaces in ' . $region . ' region');
        foreach ($spaces as $space) {
            $message = "GUEST";
            if ($currentUserId == $space->get("owner_id")) {
                $message = "OWNER";
            }

            $message = date_format(date_create($space->get("created_at")), "Y-m-d") . " " . $message;
            twoColumnItem(
                str_pad((string) $space->get("id"), 8, ".")
            . " " .
            $space->get("name"),
                $message,
            );
        }

        subtitle('Found ' . $spaces->count() . ' spaces in ' . $region . ' region');

        exit();
    }



}
