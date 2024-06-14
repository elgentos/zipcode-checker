<?php

namespace Elgentos\ZipcodeChecker\Api\Data;

use Elgentos\ZipcodeChecker\Enum\FormModeEnum;

interface CountryFormModifierInterface
{
    public function isDefault(): bool;
    public function getCountryCodes(): array;
    public function getMode(): FormModeEnum;
}
