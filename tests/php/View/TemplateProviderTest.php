<?php

namespace DNADesign\BrowserUpdate\Tests\View;

use DNADesign\BrowserUpdate\Extension\SiteConfigExtension;
use DNADesign\BrowserUpdate\Model\Announcement;
use DNADesign\BrowserUpdate\View\TemplateProvider;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\SSViewer;

final class TemplateProviderTest extends SapphireTest
{
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

        /** @var SiteConfigExtension $siteConfig */
        $siteConfig = SiteConfig::current_site_config();
        $siteConfig->BrowserAnnouncementID = $announcement->ID;

        $template = SSViewer::execute_string(
            '{$BrowserUpdate}',
            ''
        );

        $this->assertStringContainsString(
            '{"reminder":"24","reminderClosed":"150","test":"0","newwindow":"1","url":null,"noclose":"0","no_permanent_hide":"0","api":"2024.07","text":{"msg":"Your web browser ({brow_name}) is out of date.","msgmore":"Update your browser for more security, speed and the best experience on this site.","bupdate":"Update browser","bignore":"Ignore","remind":"You will be reminded in {days} days.","bnever":"Never show again"}}',
            $template,
        );
    }
}
