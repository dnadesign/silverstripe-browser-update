<?php

namespace DNADesign\BrowserUpdate\View;

use DNADesign\BrowserUpdate\Model\Announcement;
use JsonException;
use SilverStripe\View\TemplateGlobalProvider;
use function json_encode;
use function sprintf;
use const JSON_THROW_ON_ERROR;

/**
 * @see \DNADesign\BrowserUpdate\Tests\View\TemplateProviderTest
 */
class TemplateProvider implements TemplateGlobalProvider
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function get_template_global_variables(): array
    {
        return [
            'BrowserUpdate' => [
                'method' => 'getBrowserUpdate',
                'casting' => 'HTMLFragment',
            ],
        ];
    }

    public static function getBrowserUpdate(): string
    {
        $announcement = Announcement::get()->sort('Sort')->first();

        if (!$announcement instanceof Announcement) {
            return '';
        }

        try {
            $browserUpdateConfig = json_encode($announcement->getBrowserUpdateConfig(), JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return '';
        }

        return sprintf(<<<'HTML'
<script> 
    var $buoop = JSON.parse('%s'); 
    
    function $buo_f() { 
        var e = document.createElement("script"); 
        e.src = "//browser-update.org/update.min.js"; 
        document.body.appendChild(e);
    };

    try {
        document.addEventListener("DOMContentLoaded", $buo_f, false);
    }
    catch(e) {
        window.attachEvent("onload", $buo_f);
    }
</script>
HTML, $browserUpdateConfig);
    }
}
