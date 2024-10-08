<?php

namespace DNADesign\BrowserUpdate\Admin;

use DNADesign\BrowserUpdate\Model\Announcement;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\ORM\DataObject;

class AnnouncementAdmin extends ModelAdmin
{
    /**
     * @var array<class-string<DataObject>>
     */
    private static array $managed_models = [
        Announcement::class,
    ];

    private static string $url_segment = 'browser-update-announcements';

    private static string $menu_title = 'Browser update announcements';

    private static string $menu_icon_class = 'font-icon-attention-1';
}
