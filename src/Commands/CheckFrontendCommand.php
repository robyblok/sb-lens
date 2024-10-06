<?php

namespace StoryblokLens\Commands;

use HiFolks\DataType\Block;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CheckFrontendCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('check-frontend')
            ->addArgument(
                name: 'url',
                mode: InputOption::VALUE_REQUIRED,
                description: "URL to analyze (example https://www.storyblok.com/)",
                suggestedValues: [
                    "https://www.storyblok.com/",
                ],
            )
            ->addOption(
                'skip-check',
                null,
                InputOption::VALUE_NONE,
                'Skip the check with Lighthouse, only loads saved results from a previous check',
            )
            ->setDescription('Check the frontend URL and provides some suggestion to improve the Storyblok configuration.');
    }

    private static function sanitize_url($url)
    {
        // Replace all characters that are not alphanumeric or hyphens with underscores
        $sanitized_url = preg_replace('/[^a-zA-Z0-9-]/', '_', $url);
        // Remove the initial https___ (or http___ if necessary)
        $sanitized_url = preg_replace('/^https___/', '', $sanitized_url);
        // Remove trailing underscores
        $sanitized_url = rtrim($sanitized_url, '_');
        return $sanitized_url;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../resources/views');
        $twig = new \Twig\Environment(
            $loader,
            /*[
            'cache' => __DIR__ . '/../../cache',
        ]*/
        );

        $urlToAnalyze = $input->getArgument("url");
        $skipCheck = $input->getOption("skip-check");
        $urlSlug = self::sanitize_url($urlToAnalyze);

        if (! $skipCheck) {
            $process = new Process([
                'node',
                './node_modules/lighthouse/cli/index.js',
                $urlToAnalyze,
                '--output=html,json',
                '--quiet',
                '--chrome-flags="--headless"',
                '--output-path=./sb-' . $urlSlug,
            ]);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

        }
        $directory = __DIR__ . "/../../resources/views/hints";
        $fileNames = [];

        // Check if the directory exists
        if (is_dir($directory)) {
            // Open the directory
            if ($handle = opendir($directory)) {
                // Loop through the directory contents
                while (false !== ($file = readdir($handle))) {
                    // Skip the current and parent directory entries
                    if ($file != '.' && $file != '..') {
                        // Get the file name without the extension
                        $fileName = pathinfo($file, PATHINFO_FILENAME);
                        // Add the file name to the array
                        $fileNames[] = $fileName;
                    }
                }
                // Close the directory handle
                closedir($handle);
            }
        } else {
            echo "Directory not found.";
        }


        $data = Block::fromJsonFile('sb-' . $urlSlug . '.report.json');
        $template = $twig->load('frontend.md');

        $output->write(
            $template->render([
                'urlToAnalyze' => $urlToAnalyze,
                'data' => $data,
                'fileNames' => $fileNames,
            ]),
        );



        return Command::SUCCESS;
    }
}
