<?php

namespace DNADesign\BrowserUpdate\Extension;

use DNADesign\BrowserUpdate\Model\Announcement;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * @extends Extension<(SiteConfig & static)>
 *
 * @method Announcement BrowserAnnouncement()
 * @property int $BrowserAnnouncementID
 *
 * @see \DNADesign\BrowserUpdate\Tests\Extension\SiteConfigExtensionTest
 */
class SiteConfigExtension extends Extension
{
    /**
     * @var array<class-string<DataObject>>
     */
    private static array $has_one = [
        'BrowserAnnouncement' => Announcement::class,
    ];

    protected function updateCMSFields(FieldList $fields): void
    {
        $fields->addFieldsToTab('Root.BrowserUpdate', [
            DropdownField::create('BrowserAnnouncementID', 'Active browser announcement')
                ->setDescription('This announcement will display on all pages unless dismissed by the user.')
                ->setSource(Announcement::get()->map())
                ->setEmptyString('None'),
        ]);
    }
}
