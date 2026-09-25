<?php

declare(strict_types=1);

namespace DNADesign\BrowserUpdate\Tests\View;

use DNADesign\BrowserUpdate\Model\Announcement;
use DNADesign\BrowserUpdate\View\TemplateProvider;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\TemplateEngine\SSTemplateEngine;
use SilverStripe\View\ViewLayerData;

final class TemplateProviderTest extends SapphireTest
{
    /**
     * @return Iterator<int, array{string}>
     */
    public static function templateGlobalVariablesProvider(): Iterator
    {
        yield ['BrowserUpdate'];
    }

    #[DataProvider('templateGlobalVariablesProvider')]
    public function testTemplateGlobalVariables(string $key): void
    {
        $this->assertArrayHasKey(
            $key,
            TemplateProvider::get_template_global_variables()
        );
    }

    public function testBrowserUpdateNoAnnouncements(): void
    {
        $template = SSTemplateEngine::create()->renderString(
            '{$BrowserUpdate}',
            ViewLayerData::create([])
        );

        $this->assertEmpty($template);
    }

    public function testBrowserUpdate(): void
    {
        $announcement = Announcement::create();
        $announcement->write();

        $siteConfig = SiteConfig::current_site_config();
        $siteConfig->BrowserAnnouncementID = $announcement->ID;

        $template = SSTemplateEngine::create()->renderString(
            '{$BrowserUpdate}',
            ViewLayerData::create([])
        );

        $this->assertStringContainsString(
            '{"reminder":24,"reminderClosed":150,"test":0,"newwindow":1,"url":null,"noclose":0,"no_permanent_hide":0,"api":"2024.07","insecure":1,"unsupported":1,"text":{"msg":"Your web browser ({brow_name}) is out of date.","msgmore":"Update your browser for more security, speed and the best experience on this site.","bupdate":"Update browser","bignore":"Ignore","remind":"You will be reminded in {days} days.","bnever":"Never show again"}}',
            $template,
        );
    }
}
