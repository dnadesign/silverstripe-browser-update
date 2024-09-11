<?php

namespace DNADesign\BrowserUpdate\Model;

use DNADesign\BrowserUpdate\Concern\MessageFields;
use DNADesign\BrowserUpdate\Contract\BrowserUpdateInterface;
use DNADesign\BrowserUpdate\Enum\Browser;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use function sprintf;

/**
 * @property string $Browser
 * @property float $Version
 * @property ?string $Msg
 * @property ?string $Msgmore
 * @property ?string $Bupdate
 * @property ?string $Bignore
 * @property ?string $Remind
 * @property ?string $Bnever
 * @property int $AnnouncementID
 * @method Announcement Announcement()
 */
class BrowserVersion extends DataObject implements BrowserUpdateInterface
{
    use MessageFields;

    private static string $table_name = 'BrowserUpdate_BrowserVersion';

    private static string $singular_name = 'Browser version';

    private static string $plural_name = 'Browser versions';

    /**
     * @var string[]
     */
    private static array $db = [
        'Browser' => 'Varchar(100)',
        'Version' => 'Double',
        'Msg' => 'Varchar(255)',
        'Msgmore' => 'Varchar(255)',
        'Bupdate' => 'Varchar(255)',
        'Bignore' => 'Varchar(255)',
        'Remind' => 'Varchar(255)',
        'Bnever' => 'Varchar(255)',
    ];

    /**
     * @var array<class-string<DataObject>>
     */
    private static array $has_one = [
        'Announcement' => Announcement::class,
    ];

    /**
     * @var string[]
     */
    private static array $summary_fields = [
        'getTitle' => 'Name',
        'Version' => 'Version',
    ];

    public function getTitle(): string
    {
        $browser = Browser::tryFrom($this->Browser);

        if (!$browser instanceof Browser) {
            return (string) $this->ID;
        }

        return $browser->name;
    }

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        BrowserVersion::removeMessageFields($fields);

        $fields->removeByName([
            'AnnouncementID',
        ]);

        $fields->addFieldsToTab('Root.Main', [
            DropdownField::create(
                'Browser',
                'Browser',
                Browser::getDropdownFieldOptions()
            ),
            NumericField::create('Version', 'Version')
                ->setDescription(
                    '<p class="alert alert-info">' .
                    'If a positive number is selected then all browser versions below that will be notified. (i.e. 22 will notifiy < 22 if the latest version is 40)<br />' .
                    'If a negative number is selected then browsers that are x versions behind will be notified. (i.e. -5 will notify < 35 if the latest version is 40)<br />' .
                    'If 0 is selected then the latest version of the browser required.' .
                    '</p>'
                ),
            BrowserVersion::getMessageCompositeField(),
        ]);

        $this->extend('updateCMSFields', $fields);

        /** @phpstan-ignore-next-line return.type */
        return $fields;
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();

        $validator->addValidator(RequiredFields::create([
            'Browser',
            'Version',
        ]));

        return $validator;
    }

    public function getBrowserUpdateConfig(): array
    {
        $textForKeyName = sprintf('text_for_%s', $this->Browser);
        $messageConfig = $this->getMessageConfigArray();

        $fields = [
            'required' => [
                $this->Browser => $this->Version,
            ],
        ];

        if ($messageConfig === []) {
            return $fields;
        }

        return [
            ...$fields,
            $textForKeyName => $messageConfig,
        ];
    }
}
