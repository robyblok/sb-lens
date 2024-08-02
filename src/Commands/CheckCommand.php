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

    private function getPlanDescription($planLevel): string
    {
        return match ($planLevel) {
            0 => 'Starter (Trial)',
            2 => 'Enterprise (Trial)',
            1 => 'Premium (Trial)',
            1000 => 'Development',
            100 => 'Community',
            200 => 'Entry',
            300 => 'Teams',
            301 => 'Business',
            400 => 'Enterprise',
            500 => 'Enterprise Plus',
            default => $planLevel,
        };

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $spaceId = $input->getOption("space");
        $r = new Reporter();

        $r->title('Storyblok Lens - Check Space ' . $spaceId);

        $client = SbClient::make();

        $content = $client->space()->spaceId($spaceId)->get();
        file_put_contents(
            "./space_" . $spaceId . ".json",
            json_encode($content, JSON_PRETTY_PRINT),
        );
        $space = $content["space"];
        $statistics = $client
            ->statistics()
            ->spaceId($spaceId)
            ->get();
        var_dump($statistics);
        $traffic = $client
            ->traffic()
            ->spaceId($spaceId)
            ->get();
        $components = $client
            ->components()
            ->spaceId($spaceId)
            ->get();

        $r->title(sprintf('Checking Space (%s): %s', $spaceId, $space['name']), 2);
        $r->liLabelValue("Plan", $this->getPlanDescription($space['plan_level']));
        $r->liLabelValue("Traffic usage (this month)", Resultset::formatBytes($traffic['traffic_used_this_month']));
        $r->liLabelValue("Traffic usage (last 5 days)", Resultset::formatBytes($traffic['total_traffic_per_time_period']));
        $r->liLabelValue("API Requests (last 5 days)", $traffic['total_requests_per_time_period']);
        $r->liLabelValue("API Server Location", $space['region']);
        $r->liLabelValue("Stories", $space['stories_count']);
        $r->liLabelValue("Assets", $space['assets_count']);
        $r->liLabelValue("Blocks/Components", $components->count());
        $r->liLabelValue("Users", $statistics->get("collaborators_count", "N/A"));
        $r->liLabelValue("Max Users", $statistics->get("collaborators_limit", "N/A"));
        $r->newLine();

        $r->title("Traffic usage (monthly)");

        $r->tableHeader(["Month", "Requests", "Traffic"]);
        foreach ($statistics["monthly_traffic"] as $statistic) {
            $r->tableRow(
                [
                    $statistic["month_col"],
                    $statistic["counting"] . " reqs.",
                    Resultset::formatBytes($statistic["total_bytes"]),
                ],
            );

        }

        $r->newLine();
        $r->title("Installed applications");


        $apps = $client->apps()->spaceId($spaceId)->get();
        $hasDimensionApp = false;
        $hasPipelineApp = false;
        $hasBackupApp = false;
        $hasTaskApp = false;
        $r->paragraph(sprintf('The Space %s has ', $spaceId) . count($apps["apps"]) . " installed applications.");
        $r->paragraph("The installed applications are:");
        foreach ($apps["apps"] as $app) {
            if ($app['slug'] === 'backups') {
                $hasBackupApp = true;
            }

            if ($app['slug'] === 'tasks') {
                $hasTaskApp = true;
            }

            if ($app['slug'] === 'locales') {
                $hasDimensionApp = true;
            }

            if ($app['slug'] === 'branches') {
                $hasPipelineApp = true;
            }

            $r->li(sprintf('%s (%s)', $app['name'], $app['slug']));

        }

        $r->newLine();

        $r->title('Project Structure');
        $r->title("Applications for managing the Space's Structure", 3);

        if ($hasDimensionApp) {
            $r->paragraph('The first level folder structure is managed via Dimension Application');
        } else {
            $r->paragraph('Dimension Application not installed.');
        }

        if ($hasPipelineApp) {
            $r->paragraph('Using Pipeline app with:');
            $branches = $client->branches()->spaceId($spaceId)->get();
            foreach ($branches["branches"] as $branch) {
                $r->li($branch['name']);
            }
        } else {
            $r->paragraph('Pipeline App is NOT used.');
        }

        $r->title('First level folders');

        $folders = $client->stories()->spaceId($spaceId)
            ->onlyFolder()
            ->parentId(0)->get();

        $r->paragraph('First level folders:');
        foreach ($folders["stories"] as $folder) {
            $r->li(sprintf('%s ( %s )', $folder['name'], $folder['slug']));
        }

        $r->newLine();

        $r->title('Block Library usage');

        $components = $client
            ->components()
            ->spaceId($spaceId)
            ->get();

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

            if ($string === "") {
                $string = "Nestable";
                ++$numNestable;
            }

            $string = $string . " with " . count($component["all_presets"]) . " presets";
            if (count($component["all_presets"]) > 0) {
                ++$numWithPreset;
            }

        }

        $r->paragraph('Component usage, how many content types, nestable components etc: ');
        $r->liLabelValue("Content Types", $numContentTypes);
        $r->liLabelValue("Universal", $numUniversal);
        $r->liLabelValue("Nested Block", $numNestable);
        $r->liLabelValue("Component with presets", $numWithPreset);

        $presets = $client
            ->presets()
            ->spaceId($spaceId)
            ->get();

        $r->liLabelValue("Total presets", count($presets["presets"]));


        $output->write($r->getString());

        //View::make('report', ['name' => 'James']);



        exit();
    }



}
