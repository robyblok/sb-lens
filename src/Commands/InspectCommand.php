<?php

namespace StoryblokLens\Commands;

use StoryblokLens\Resultset;

use StoryblokLens\SbClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function StoryblokLens\{hint, title, subtitle, twoColumnItem, twoColumnList};

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
            ->addOption(
                'url',
                'u',
                InputOption::VALUE_OPTIONAL,
                'URL to analyze (example https://www.storyblok.com/)',
                ''
            )
            ->addOption(
                'skip-url',
                null,
                InputOption::VALUE_NONE,
                'Skip the HTTP URL analysis'
            )
            ->setDescription('Inspect some Storyblok space configuration.');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $spaceId = $input->getOption("space");
        $urlToAnalyze = $input->getOption("url");
        $skipUrl = $input->getOption("skip-url");

        title("Storyblok Lens");

        $client = SbClient::make();


        $content = $client->space()->spaceId($spaceId)->get();
        file_put_contents(
            "./space_" . $spaceId . ".json",
            json_encode($content, JSON_PRETTY_PRINT)
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
        $apps = $client->apps()->spaceId($spaceId)->get();
        subtitle("Applications");
        twoColumnList($apps["apps"], ["name", "intro"]);

        $statistics = $client
            ->statistics()
            ->spaceId($spaceId)
            ->get();
        $resultSpace->reset($statistics);
        $resultSpace->add("collaborators_count");
        $resultSpace->add("all_stories_count");
        $resultSpace->add("all_assets_count");
        $resultSpace->add("all_components_count");
        subtitle("Metrics");
        $resultSpace->viewResult();
        //print_r($statistics["api_logs"]);
        subtitle("Monthly traffic");
        foreach ($statistics["monthly_traffic"] as $statistic) {
            $string = "(" . $statistic["counting"] . " reqs.) " .
                Resultset::formatBytes($statistic["total_bytes"]);
            twoColumnItem($statistic["month_col"], $string);

        }

        /*
        subtitle("API calls, last week");
        foreach ($statistics["monthly_traffic"] as $statistic) {
            $string = "(" . $statistic["created_at"] . " reqs.) " .
                Resultset::formatBytes($statistic["total_bytes"]);
            twoColumnItem($statistic["month_col"], $string);

        }
        */


        $components = $client
            ->components()
            ->spaceId($spaceId)
            ->get();
        //print_r($statistics["api_logs"]);
        subtitle("Components");
        $numContentTypes = 0;
        $numNestable = 0;
        $numUniversal = 0;
        $numWithPreset = 0;
        foreach ($components["components"] as $component) {
            $string = "";
            if ($component["is_root"] && $component["is_nestable"]) {
                $string = "Universal Block";
                ++$numUniversal;
            } else {
                if ($component["is_root"]) {
                    $string = "Content Type";
                    ++$numContentTypes;
                }

                if ($component["is_nestable"]) {
                    $string = "Nestable";
                    ++$numNestable;
                }
            }

            if ($string == "") {
                $string = "Nestable";
                ++$numNestable;
            }

            $string = $string . " with " . count($component["all_presets"]) . " presets";
            if (count($component["all_presets"]) > 0) {
                ++$numWithPreset;
            }

            //twoColumnItem($component["name"], $string);
        }

        twoColumnItem("Content Types", $numContentTypes);
        twoColumnItem("Universal", $numUniversal);
        twoColumnItem("Nested Block", $numNestable);
        twoColumnItem("Component with presets", $numWithPreset);
        $presets = $client
            ->presets()
            ->spaceId($spaceId)
            ->get();
        $resultPresets = Resultset::make($presets);
        $resultPresets->addItemResult("Presets count", count($presets["presets"]));
        subtitle("Presets");
        $resultPresets->viewResult();

        /*
        $cdnStories = $client
            ->cdnStories()
            ->get();
        subtitle("Stories for space id " . $spaceId);
        twoColumnList($cdnStories["stories"], ["id", "name"]);
        */

        $workflows = $client
            ->workflows()
            ->spaceId($spaceId)
            ->get();

        $workflows = $workflows["workflows"];

        $availableWorkflows = [];
        subtitle("Workflows, found " . count($workflows) . " workflows");
        foreach ($workflows as $workflow) {
            $defaultString = $workflow["is_default"] ? " - [DEFAULT]" : "";
            subtitle(
                subtitle: "Workflow  : " . $workflow["name"] . $defaultString,
                colorLevel: "300",
                padding: "3",
                margin: "3",
                textColor: "gray-900"
            );
            foreach($workflow["workflow_stages"] as $stage) {
                $allowReport = [];
                $availableWorkflows[$stage["id"]] = $stage["name"];
                foreach (["allow_all_stages", "allow_all_users", "allow_publish"] as $allow) {
                    if ($stage[$allow]) {
                        $allowReport[] = Resultset::unslugifyString($allow);
                    }
                }

                twoColumnItem($stage["name"], implode(", ", $allowReport));
            }
        }

        $stories = $client
            ->stories()
            ->spaceId($spaceId)
            ->get();
        subtitle("Stories without workflow for space id: " . $spaceId);
        //var_dump($response->getHeaders()["total"][0]);
        foreach ($stories["stories"] as $item) {
            $prefix = $item["is_folder"] ? "F" : "S";
            $stage = is_null($item["stage"]) ? "NO STAGE WORKFLOW" : $availableWorkflows[$item["stage"]["workflow_stage_id"]];

            //twoColumnItem($prefix . " - " . $stage . "  - " . $item["name"], $item["full_slug"]);


        }

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

        if (! $skipUrl) {
            if ($urlToAnalyze !== "") {
                $process = new Process(['bun',
                    'run',
                    './node_modules/lighthouse/cli/index.js',
                    $urlToAnalyze,
                    '--output=html,json',
                    '--quiet',
                    '--chrome-flags="--headless"',
                    '--output-path=./sb'
                ]);
                $process->run();

                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                echo $process->getOutput();

            }

            $json = file_get_contents('sb.report.json');
            $json_data = json_decode($json, true);
            foreach ($json_data["audits"] as $audit) {
                if ($audit["score"] < 0.7 && $audit["score"] != "") {
                    hint(
                        $audit["title"],
                        //""
                        $audit["description"]
                    );
                    //print_r($audit);
                }

            }

        }

        return Command::SUCCESS;
    }



}
