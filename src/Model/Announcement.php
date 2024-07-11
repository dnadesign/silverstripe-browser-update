<?php

namespace DNADesign\BrowserUpdate\Model;

use DNADesign\BrowserUpdate\Concern\MessageFields;
use DNADesign\BrowserUpdate\Contract\BrowserUpdateInterface;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;

/**
 * @property int $Reminder
 * @property int $ReminderClosed
 * @property bool $Test
 * @property bool $NewWindow
 * @property string $Url
 * @property bool $NoClose
 * @property bool $NoMessage
 * @property bool $NoPermanentHide
 * @property int $Sort
 * @method HasManyList<BrowserVersion> BrowserVersions()
 */
class Announcement extends DataObject implements BrowserUpdateInterface
{
    use MessageFields;

    private static string $table_name = 'BrowserUpdate_Announcement';

    private static string $singular_name = 'Browser update announcement';

    private static string $plural_name = 'Browser update announcements';

    /**
     * @var string[]
     */
    private static array $db = [
        'Name' => 'Varchar(255)',
        'Msg' => 'Varchar(255)',
        'Msgmore' => 'Varchar(255)',
        'Bupdate' => 'Varchar(255)',
        'Bignore' => 'Varchar(255)',
        'Remind' => 'Varchar(255)',
        'Bnever' => 'Varchar(255)',
        'Reminder' => 'Int',
        'ReminderClosed' => 'Int',
        'Test' => 'Boolean(0)',
        'NewWindow' => 'Boolean(1)',
        'Url' => 'Varchar(255)',
        'NoClose' => 'Boolean(0)',
        'NoMessage' => 'Boolean(0)',
        'NoPermanentHide' => 'Boolean(0)',
        'Sort' => 'Int',
    ];

    /**
     * @var array<mixed>
     */
    private static array $defaults = [
        'Msg' => 'Your web browser ({brow_name}) is out of date.',
        'Msgmore' => 'Update your browser for more security, speed and the best experience on this site.',
        'Bupdate' => 'Update browser',
        'Bignore' => 'Ignore',
        'Remind' => 'You will be reminded in {days} days.',
        'Bnever' => 'Never show again',
        'Reminder' => 24,
        'ReminderClosed' => 150,
    ];

    /**
     * @var array<class-string<DataObject>>
     */
    private static array $has_many = [
        'BrowserVersions' => BrowserVersion::class,
    ];

    /**
     * @var string[]
     */
    private static array $owns = [
        'BrowserVersions',
    ];

    /**
     * @var string[]
     */
    private static array $cascade_deletes = [
        'BrowserVersions',
    ];

    /**
     * @var string[]
     */
    private static array $summary_fields = [
        'Title' => 'Title',
    ];

    private static string $browser_update_api_version = '2024.07';

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $gridFieldConfig = GridFieldConfig_RelationEditor::create();

        $gridField = GridField::create(
            'BrowserVersions',
            'Browser versions',
            $this->BrowserVersions(),
            $gridFieldConfig
        );

        self::removeMessageFields($fields);

        $fields->removeByName([
            'Sort',
            'BrowserVersions',
        ]);

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', 'Title')
                ->setDescription('For CMS reference only.'),
            self::getMessageCompositeField(),
            $gridField,
            NumericField::create('Reminder', 'reminder')
                ->setDescription('After how many hours should the message reappear (0 = show all the time).'),
            NumericField::create('ReminderClosed', 'reminderClosed')
                ->setDescription('If the user explicitly closes message it reappears after x hours.'),
            CheckboxField::create('Test', 'test')
                ->setDescription('True = always show the bar (for testing).'),
            CheckboxField::create('NewWindow', 'newwindow')
                ->setDescription('Open link in new window/tab.'),
            TextField::create('Url', 'url')
                ->setDescription('the url to go to after clicking the notification.'),
            CheckboxField::create('NoClose', 'noclose')
                ->setDescription('Do not show the "ignore" button to close the notification.'),
            CheckboxField::create('NoMessage', 'nomessage')
                ->setDescription('Do not show a message if the browser is out of date'),
            CheckboxField::create('NoPermanentHide', 'no_permanent_hide')
                ->setDescription('Do not give the user the option to permanently hide the notification'),
        ]);

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    public function getBrowserUpdateConfig(): array
    {
        $fields = [];

        foreach ($this->BrowserVersions() as $browserVersion) {
            $fields += $browserVersion->getBrowserUpdateConfig();
        }

        $fields = [
            ...$fields,
            'reminder' => (string) $this->Reminder,
            'reminderClosed' => (string) $this->ReminderClosed,
            'test' => (string) $this->Test,
            'newwindow' => (string) $this->NewWindow,
            'url' => $this->Url,
            'noclose' => (string) $this->NoClose,
            'no_permanent_hide' => (string) $this->NoPermanentHide,
            'api' => self::config()->get('browser_update_api_version'),
        ];

        $text = $this->getMessageConfigArray();

        if ($text === []) {
            return $fields;
        }

        return [
            ...$fields,
            'text' => $text,
        ];
    }
}
