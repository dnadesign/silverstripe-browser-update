<?php

namespace DNADesign\BrowserUpdate\Tests\View;

use DNADesign\BrowserUpdate\Model\Announcement;
use DNADesign\BrowserUpdate\View\TemplateProvider;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\View\SSViewer;

final class TemplateProviderTest extends SapphireTest
{
    protected function setUp(): void
    {
        parent::setUp();

        Announcement::get()->removeAll();
    }

    /**
     * @return array<array<string>>
     */
    public function templateGlobalVariablesProvider(): array
    {
        return [
            ['BrowserUpdate'],
        ];
    }

    /**
     * @dataProvider templateGlobalVariablesProvider
     */
    public function testTemplateGlobalVariables(string $key): void
    {
        $this->assertArrayHasKey(
            $key,
            TemplateProvider::get_template_global_variables()
        );
    }

    public function testBrowserUpdateNoAnnouncements(): void
    {
        $template = SSViewer::execute_string(
            '{$BrowserUpdate}',
            ''
        );

        $this->assertEmpty($template);
    }

    public function testBrowserUpdate(): void
    {
        $announcement = Announcement::create();
        $announcement->write();

        $template = SSViewer::execute_string(
            '{$BrowserUpdate}',
            ''
        );

        $this->assertStringContainsString(<<<'HTML'
<script> 
    var $buoop = JSON.parse('{"reminder":"24","reminderClosed":"150","test":"0","newwindow":"1","url":null,"noclose":"0","no_permanent_hide":"0","api":"2024.07","text":{"msg":"Your web browser ({brow_name}) is out of date.","msgmore":"Update your browser for more security, speed and the best experience on this site.","bupdate":"Update browser","bignore":"Ignore","remind":"You will be reminded in {days} days.","bnever":"Never show again"}}'); 
    
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
HTML, $template);
    }
}
