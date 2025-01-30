<?php

namespace App\Twig;

use HTMLPurifier;
use HTMLPurifier_Config;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('purifier', [$this, 'purifyMessage']),
        ];
    }

    public function purifyMessage($message)
    {
        $purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());
        return $purifier->purify($message); // Retourne le message purifié
    }
}