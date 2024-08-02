<?php

namespace StoryblokLens;

class Reporter
{
    private string $result = "";

    private int $titleLevel = 1;


    public function title(string $title, $titleLevel = null): string
    {
        if (! is_null($titleLevel)) {
            $this->titleLevel = $titleLevel;
        }

        return $this->appendString(str_repeat("#", $this->titleLevel) .
            " " . $title) . $this->newLine();
    }

    public function paragraph(string $string): string
    {
        return $this->appendString($string) . $this->newLine();
    }

    public function li(string $string): string
    {
        return $this->appendString("- " . $string);

    }

    public function newLine(): string
    {
        return $this->appendString("", withEOL: true);

    }

    public function liLabelValue($label, $value): string
    {
        return $this->li(sprintf('%s: %s', $label, $value));
    }


    public function tableHeader(array $columnsName = ["Key", "Value"]): string
    {
        if ($columnsName === []) {
            return "";
        }

        $columnRow = implode(" | ", $columnsName);
        return  $this->appendString(
            sprintf('| %s |', $columnRow),
        ) .
            $this->appendString(
                "| " . str_repeat(" --- |", count($columnsName)),
            );



    }

    public function tableRow(array $cells): string
    {
        if ($cells === []) {
            return "";
        }

        $cellRow = implode(" | ", $cells);
        return  $this->appendString(
            sprintf('| %s | ', $cellRow),
        );
    }

    public function appendString(string $string, $withEOL = true): string
    {
        $this->result .= $string . ($withEOL ? PHP_EOL : "");
        return $string;
    }


    public function getString(): string
    {
        return $this->result;

    }



}
