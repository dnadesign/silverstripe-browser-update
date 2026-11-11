<?php

declare(strict_types=1);

namespace DNADesign\BrowserUpdate\Contract;

interface BrowserUpdateInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getBrowserUpdateConfig(): array;
}
