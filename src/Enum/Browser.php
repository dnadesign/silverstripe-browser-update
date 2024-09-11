<?php

namespace DNADesign\BrowserUpdate\Enum;

enum Browser: string
{
    case Chrome = 'c';

    case Firefox = 'f';

    case IE = 'i';

    case Opera = 'o';

    case Safari = 's';

    /**
     * @return string[]
     */
    public static function getDropdownFieldOptions(): array
    {
        $options = [];

        foreach (Browser::cases() as $case) {
            $options[$case->value] = $case->name;
        }

        return $options;
    }
}
