<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class ProductHtml
{
    public function clean(?string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.Allowed', 'p[style],br,strong,b,em,i,u,s,strike,h2[style],h3[style],h4[style],ul,ol[start],li,blockquote,pre,code,hr,a[href|title],img[src|alt|title|width|height],table,thead,tbody,tfoot,tr,th[colspan|rowspan],td[colspan|rowspan]');
        $config->set('CSS.AllowedProperties', ['text-align']);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $config->set('HTML.Nofollow', true);

        return (new HTMLPurifier($config))->purify($html ?? '');
    }
}
