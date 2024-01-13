<?php

namespace StoryblokLens\Commands;

use Dotenv\Dotenv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

class InspectCommand extends Command
{
    private ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientCdn = null;

    private ?\Symfony\Contracts\HttpClient\HttpClientInterface $clientMapi = null;

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

    private function setupClient(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();

        $this->clientCdn = HttpClient::create()
            ->withOptions([
                'base_uri' => 'https://api.storyblok.com/v2',
                'headers' =>
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
        $this->clientMapi = HttpClient::create()
            ->withOptions([
                'base_uri' => 'https://mapi.storyblok.com',
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => $_ENV["STORYBLOK_OAUTH_TOKEN"]
                    ],
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $spaceId = $input->getOption("space");

        $io->title("Storyblok Lens");
        $this->setupClient();

        $response = $this->clientCdn->request(
            'GET',
            'v2/cdn/spaces/me',
            [
                'query' => [
                    'token' => $_ENV["STORYBLOK_ACCESS_TOKEN"]
                ],
            ]
        );

        // $statusCode = $response->getStatusCode();
        $io->section('Inspecting Space: ' . $spaceId);
        $content = $response->toArray();
        $resultSpace = [];

        foreach ($content['space'] as $key => $value) {
            $rowValue = $value;
            if (is_array($value)) {
                $rowValue = implode(", ", $value);
                //$resultSpace[$key] = implode($value);
            }

            $resultSpace[] = [
                $key,
                $rowValue
            ];
        }

        //var_dump($resultSpace);
        /*
        $output->writeln(" Status code: " . $statusCode);
        $contentType = $response->getHeaders()['content-type'][0];
        $output->writeln(" Content Type: " . $contentType);
        // $contentType = 'application/json'
        $content = $response->getContent();
        $output->writeln(" Content Size: " . strlen($content));
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        //var_dump($content);
        $output->writeln("<info>Story</>: <" . $content["space"]["name"] . "");
        */
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]
        $response = $this->clientMapi->request(
            'GET',
            'v1/spaces/' . $spaceId
        );
        $content = $response->toArray();
        $space = $content["space"];

        $resultSpace[] = [
            "Stories count",
            $space["stories_count"]
        ];
        $resultSpace[] = [
            "Assets count",
            $space["assets_count"]
        ];
        $resultSpace[] = [
            "Region",
            $space["region"]
        ];


        $io->table(
            ['Info', 'Value'],
            $resultSpace
        );

        $io->section("Limits");

        $resultLimits = [];
        foreach ($space['limits'] as $key => $value) {
            $rowValue = $value;
            if (is_array($value)) {
                $rowValue = implode(", ", $value);
                //$resultSpace[$key] = implode($value);
            }

            if ($key == "traffic_limit") {
                $rowValue = $this->formatBytes($rowValue, 0);
            }

            $resultLimits[] = [
                ucwords(str_replace('_', ' ', (string) $key)),
                $rowValue
            ];
        }

        $io->table(
            ['Feature', 'Limited'],
            $resultLimits
        );


        $response = $this->clientMapi->request(
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
        $apps = $content["apps"];

        $appList = [];
        foreach ($apps as $app) {
            $appList[] = [
              $app["name"],
                $app["intro"],
            ];
        }

        $io->table(
            ['Installed App', 'Info'],
            $appList
        );


        $response = $this->clientMapi->request(
            'GET',
            sprintf('v1/spaces/%s/statistics', $spaceId),
            [
                'query' => [
                    'version' => 'new'
                ],
            ]
        );
        $content = $response->toArray();

        $statistics = [];
        $statistics[] = [
            "Collaborators",
            $content["collaborators_count"]
        ];
        $statistics[] = [
            "Stories",
            $content["all_stories_count"]
        ];
        $statistics[] = [
            "Assets",
            $content["all_assets_count"]
        ];
        $statistics[] = [
            "Components",
            $content["all_components_count"]
        ];

        $io->table(
            ['Metrics', 'Count'],
            $statistics
        );

        return Command::SUCCESS;
    }


    public function formatBytes(string $bytes, $precision = 2): string
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;
        if ($bytes < $kilobyte) {
            return $bytes . ' B';
        }

        if ($bytes < $megabyte) {
            return number_format($bytes / $kilobyte, $precision) . ' KB';
        }

        if ($bytes < $gigabyte) {
            return number_format($bytes / $megabyte, $precision) . ' MB';
        }

        if ($bytes < $terabyte) {
            return number_format($bytes / $gigabyte, $precision) . ' GB';
        }

        return number_format($bytes / $terabyte, $precision) . ' TB';
    }

}
