<?php

namespace Elgentos\ZipcodeChecker\Model\CountryFormModifier;

use Elgentos\ZipcodeChecker\Api\Data\CountryFormModifierInterface;
use Elgentos\ZipcodeChecker\Enum\FormModeEnum;

class NLCountryModifier implements CountryFormModifierInterface
{
    public function isDefault(): bool
    {
        return false;
    }

    public function getCountryCodes(): array
    {
        return ['nl'];
    }

    public function getMode(): FormModeEnum
    {
        return FormModeEnum::SearchBasedOnZipcodeHouseNumber;
    }
}
