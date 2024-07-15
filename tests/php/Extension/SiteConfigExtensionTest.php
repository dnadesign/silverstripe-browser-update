<?php

namespace DNADesign\BrowserUpdate\Tests\Extension;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormField;
use SilverStripe\SiteConfig\SiteConfig;

final class SiteConfigExtensionTest extends SapphireTest
{
    /**
     * @return array<array{string, class-string<FormField>}>
     */
    public function updateCMSFieldsProvider(): array
    {
        return [
            ['BrowserAnnouncementID', DropdownField::class],
        ];
    }

    /**
     * @dataProvider updateCMSFieldsProvider
     * @param class-string<FormField> $fieldClass
     */
    public function testUpdateCMSFields(string $fieldName, string $fieldClass): void
    {
        $fields = SiteConfig::current_site_config()->getCMSFields();
        $field = $fields->dataFieldByName($fieldName);

        $this->assertInstanceOf($fieldClass, $field);
    }
}
