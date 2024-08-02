<?php

namespace StoryblokLens;

use Symfony\Component\Console\Style\SymfonyStyle;

use function StoryblokLens\Termwind\{twoColumnList};

class Resultset
{
    private array $result = [];

    private array $keyList = [];

    private $data;

    public function __construct($data)
    {
        $this->reset($data);
    }

    public static function make($data): self
    {
        return new self($data);
    }

    public function reset($data = []): void
    {
        $this->data = $data;
        $this->result = [];
        $this->keyList = [];

    }

    public function add($field, $function = false): static
    {
        $value = "";
        if (array_key_exists($field, $this->data)) {
            $value = $this->data[$field];
            if ($function) {
                $value = forward_static_call($function, $value);
            }
        }

        $this->result[] = [
            self::unslugifyString($field),
            $value //$this->data[$field]
        ];
        $this->keyList[] = $field;
        return $this;
    }

    public function addOthers(): static
    {
        foreach ($this->data as $key => $value) {
            if (! in_array($key, $this->keyList)) {
                $this->result[] = [
                    self::unslugifyString($key),
                    $value
                ];
                $this->keyList[] = $key;
            }
        }

        return $this;
    }

    public function addItemResult($label, $value): void
    {
        $this->result[] = [
            $label,
            $value
        ];
    }

    public function addByte($field): static
    {
        return $this->add($field, ['self','formatBytes']);
    }

    public function printResult(SymfonyStyle $io, $column1, $column2): void
    {
        $io->table([$column1, $column2], $this->result);
    }

    public function viewResult(): void
    {
        twoColumnList($this->result);
    }



    public static function formatBytes(string|null $bytes, $precision = 2): string
    {
        if (is_null($bytes)) {
            $bytes = 0;
        }

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

    public static function unslugifyString($string): string
    {
        return ucwords(str_replace('_', ' ', (string) $string));
    }

    public function printTable($data, $io, $fieldList, $columnName = []): void
    {


        $rowList = [];
        foreach ($data as $app) {
            $row = [];
            foreach ($fieldList as $field) {
                $row[] = $app[$field];
            }

            $rowList[] = $row;
        }

        if (count($fieldList) !== count($columnName)) {
            $columnName = $fieldList;
        }

        $io->table(
            $columnName,
            $rowList
        );
    }
}
