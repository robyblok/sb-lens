<?php

namespace StoryblokLens\Commands;

use StoryblokLens\Resultset;

use StoryblokLens\SbClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function StoryblokLens\{hint, title, subtitle, twoColumnList};

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
        $spaceId = $input->getOption("space");

        title("Storyblok Lens");

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
        subtitle('Inspecting Space (' . $spaceId . '): ' . $space["name"]);
        $resultSpace = Resultset::make($space);
        $resultSpace->add("name");
        $resultSpace->add("stories_count");
        $resultSpace->add("assets_count");
        $resultSpace->add("region");
        $resultSpace->viewResult();
        subtitle("Limits");
        $resultSpace->reset($space["limits"]);
        $resultSpace->add("plan_level");
        $resultSpace->addByte("traffic_limit");
        $resultSpace->add("max_collaborators");
        $resultSpace->addOthers();
        $resultSpace->viewResult();
        subtitle("Environments");
        twoColumnList($space["environments"], ["name", "location"]);
        subtitle("Languages");
        twoColumnList($space["options"]["languages"], ["code", "name"]);
        subtitle("Roles");
        twoColumnList($space["space_roles"], ["role", "id"]);
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
        subtitle("Applications");
        twoColumnList($content["apps"], ["name", "intro"]);

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
        subtitle("Metrics");
        $resultSpace->viewResult();


        $response = $client->mapi()->request(
            'GET',
            sprintf('v1/spaces/%s/presets', $spaceId)
        );
        $content = $response->toArray();
        $resultPresets = Resultset::make($content);
        $resultPresets->addItemResult("Presets count", count($content["presets"]));
        //var_dump($response->getHeaders());
        //die();
        //$resultPresets->addItemResult("Presets count", $response->getHeaders()["total"]);
        subtitle("Presets");
        $resultPresets->viewResult();
        if (count($space["space_roles"]) === 0) {
            hint(
                "Enhance user control and security by configuring roles! Roles allow you to create groups of users with specific permissions and responsibilities. For instance, you can define a 'Content Reviewer' role, granting the ability to read and save content without the power to publish. Take charge of your user management today for a more tailored and secure experience.",
                "You can configure the roles here: https://app.storyblok.com/#/me/spaces/" . $spaceId . "/settings?tab=roles"
            );
        }

        if (count($space["options"]["languages"]) === 0) {
            hint(
                "Don't forget to configure languages for more control! Ensure you've defined language settings to tailor user experiences. Take charge of your user and language configurations for a secure and customized environment.",
                "You can configure the languages here: https://app.storyblok.com/#/me/spaces/" . $spaceId . "/settings?tab=internationalization"
            );
        }

        return Command::SUCCESS;
    }



}
