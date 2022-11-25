<?php

namespace App\Sources;

use App\Scraper\Contracts\SourceInterface;

class Highload implements SourceInterface
{
    public function getUrl(): string
    {
        return "https://highload.today/uk/";
    }

    public function getName(): string
    {
        return 'highload.today';
    }

    public function getWrapperSelector(): string
    {
        return "div.lenta-item";
    }

    public function getTitleSelector(): string
    {
        return 'a h2';
    }

    public function getDescSelector(): string
    {
        return '.lenta-item p:last-child';
    }

    public function getDateSelector(): string
    {
        return 'time.time';
    }

    public function getLinkSelector(): string
    {
        return 'div.text-content a:nth-child(2)';
    }

    public function getImageSelector(): string
    {
        return '.lenta-image noscript img';
        // return "img";
    }
}
