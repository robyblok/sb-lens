<?php

namespace StoryblokLens\Commands;

use StoryblokLens\SbClient;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function StoryblokLens\Termwind\{title, subtitle, twoColumnItem};

class WhoAmICommand extends BaseStoryblokCommand
{
    protected function configure()
    {
        $this
            ->setName('whoami')
/*
            ->addOption(
                'region',
                'r',
                InputOption::VALUE_OPTIONAL,
                'The region',
                'EU',
            )
*/
            ->setDescription('Retrieve the info of the current user.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        //$region = $input->getOption("region");
        title("Storyblok Lens - WHO AM I");
        $client = SbClient::make();
        $user = $client->me()->get()->getBlock("user");

        subtitle('Hi ' . $user->get("firstname"));
        twoColumnItem("ID", $user->get("id"));
        twoColumnItem("First Name", $user->get("firstname"));
        twoColumnItem("Last Name", $user->get("lastname"));
        twoColumnItem("Friendly Name", $user->get("friendly_name"));
        twoColumnItem("Email", $user->get("email"));
        twoColumnItem("User name", $user->get("username"));
        twoColumnItem("User ID", $user->get("userid"));
        twoColumnItem("Time Zone", $user->get("timezone"));

        twoColumnItem("Has Organization", $user->get("has_org"));
        twoColumnItem("Has Partner Portal", $user->get("has_partner"));
        $userid = $client->me()->id();
        twoColumnItem("ID", $userid);

        exit();
    }



}
