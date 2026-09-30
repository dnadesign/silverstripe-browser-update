<?php

declare(strict_types=1);

namespace DNADesign\BrowserUpdate\Tests\Extension;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FormField;
use SilverStripe\SiteConfig\SiteConfig;

final class SiteConfigExtensionTest extends SapphireTest
{
    // Silverstripe expects subclasses to enable database access using this inherited property.
    // @phpstan-ignore property.phpDocType
    protected $usesDatabase = true;

    /**
     * @return Iterator<int, array{string, class-string<FormField>}>
     */
    public static function updateCMSFieldsProvider(): Iterator
    {
        yield ['BrowserAnnouncementID', DropdownField::class];
    }

    /**
     * @param class-string<FormField> $fieldClass
     */
    #[DataProvider('updateCMSFieldsProvider')]
    public function testUpdateCMSFields(string $fieldName, string $fieldClass): void
    {
        $fields = SiteConfig::current_site_config()->getCMSFields();
        $field = $fields->dataFieldByName($fieldName);

        $this->assertInstanceOf($fieldClass, $field);
    }
}
