<?php

namespace DNADesign\BrowserUpdate\Concern;

use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use function array_filter;

/**
 * @property string $Msg
 * @property string $Msgmore
 * @property string $Bupdate
 * @property string $Bignore
 * @property string $Remind
 * @property string $Bnever
 */
trait MessageFields
{
    /**
     * @var string[]
     */
    final public const MESSAGE_DB_FIELDS = [
        'Msg' => 'HTMLVarchar',
        'Msgmore' => 'HTMLVarchar',
        'Bupdate' => 'HTMLVarchar',
        'Bignore' => 'HTMLVarchar',
        'Remind' => 'HTMLVarchar',
        'Bnever' => 'HTMLVarchar',
    ];

    /**
     * @var string[]
     */
    final public const MESSAGE_DB_DEFAULTS = [
        'Msg' => 'Your web browser ({brow_name}) is out of date.',
        'Msgmore' => 'Update your browser for more security, speed and the best experience on this site.',
        'Bupdate' => 'Update browser',
        'Bignore' => 'Ignore',
        'Remind' => 'You will be reminded in {days} days.',
        'Bnever' => 'Never show again',
    ];

    public static function removeMessageFields(FieldList $fields): void
    {
        $fields->removeByName([
            'Msg',
            'Msgmore',
            'Bupdate',
            'Bignore',
            'Remind',
            'Bnever',
        ]);
    }

    public static function getMessageCompositeField(): CompositeField
    {
        return CompositeField::create([
            LiteralField::create(
                'Documentation',
                '<p class="alert alert-info">' .
                '💡 See: <a href="https://github.com/browser-update/browser-update/wiki/Details-on-configuration#custom-text">' .
                'https://github.com/browser-update/browser-update/wiki/Details-on-configuration#custom-text' .
                '</a>' .
                '</p>'
            ),
            TextareaField::create('Msg', 'msg')
                ->setDescription('i.e. "Your web browser ({brow_name}) is out of date."'),
            TextareaField::create('Msgmore', 'msgmore')
                ->setDescription('i.e. "Update your browser for more security, speed and the best experience on this site."'),
            TextField::create('Bupdate', 'bupdate')
                ->setDescription('i.e. "Update browser"'),
            TextField::create('Bignore', 'bignore')
                ->setDescription('i.e. "Ignore"'),
            TextField::create('Remind', 'remind')
                ->setDescription('i.e. "You will be reminded in {days} days."'),
            TextField::create('Bnever', 'bnever')
                ->setDescription('i.e. "Never show again"'),
        ])
            ->setTitle('Message')
            ->setName('Message');
    }

    /**
     * @return array{
     *  msg?: string,
     *  msgmore?: string,
     *  bupdate?: string,
     *  bignore?: string,
     *  remind?: string,
     *  bnever?: string,
     * }
     */
    public function getMessageConfigArray(): array
    {
        $config = [
            'msg' => $this->Msg ?? '',
            'msgmore' => $this->Msgmore ?? '',
            'bupdate' => $this->Bupdate ?? '',
            'bignore' => $this->Bignore ?? '',
            'remind' => $this->Remind ?? '',
            'bnever' => $this->Bnever ?? '',
        ];

        // Filter out empty values
        return array_filter($config, static function (string $value): bool {
            return $value !== '';
        });
    }
}
