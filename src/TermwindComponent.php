<?php

namespace StoryblokLens;

use function Termwind\render;

function title($title, $subtitle = ""): void
{
    render(<<<HTML
    <div>
        <div class="px-1 bg-green-600">{$title}</div>
        <em class="ml-1">
          {$subtitle}
        </em>
    </div>
HTML);
}

function subtitle($subtitle, $color = "blue", $colorLevel = "600", $padding = "1", $margin = 0, $textColor = "white"): void
{
    render(<<<HTML
    <div> 
        <div class="text-{$textColor} ml-{$margin} px-{$padding} bg-{$color}-{$colorLevel}">{$subtitle}</div>
    </div>
HTML);
}

function twoColumnList($list, $column = [0, 1]): void
{
    if (is_null($list)) {
        render(<<<HTML
    <div> 
        <div class="ml-2 px-1 bg-yellow-600">No data</div>
    </div>
HTML);

        return;
    }

    foreach ($list as $item) {
        twoColumnItem($item[$column[0]], $item[$column[1]]);
    }

}

function twoColumnItem($label, $value = ""): void
{
    render(<<<HTML
    <div class="flex mx-2 max-w-150">
    <span>
        {$label}
    </span>
    <span class="flex-1 content-repeat-[.] text-gray ml-1"></span>
    <span class="ml-1">
        {$value}
    </span>
    </div>
HTML);
}

function hint($message, $submessage = ""): void
{
    render(<<<HTML
        <div class="flex space-x-1">
            <span class="bg-yellow-600 text-white  font-bold">💡 Hint:</span>
            <span class="flex-1  content-repeat-[.] text-gray"></span>
            <div>
                <span class="px-2 text-yellow-400 font-bold">
                    {$message}
                </span>
                

            </div>
            <span class="px-2 text-gray-400">
                    {$submessage}
                </span>
        </div>
HTML);
}
