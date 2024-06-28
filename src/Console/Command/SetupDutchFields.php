<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Console\Command;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class SetupDutchFields extends Command
{
    const COMMAND_NAME = 'elgentos:checkout:setup-dutch-fields';
    const EAV_ATTRIBUTE_FORM_FIELD_PATH = 'hyva_themes_checkout/component/shipping_address/eav_attribute_form_fields';

    protected array $settingChanges = [
        [
            'path' => 'hyva_themes_checkout/address_form/street/field_label_0',
            'value' => 'Street'
        ],
        [
            'path' => 'hyva_themes_checkout/address_form/street/field_label_1',
            'value' => 'Housenumber'
        ]
    ];

    protected array $attributeChanges = [
        [
            'code' => 'lastname',
            'data' => [
                'length' => "1" // 1 => 50%
            ]
        ],
        [
            'code' => 'street.0',
            'data' => [
                'length' => "1" // 1 => 50%
            ]
        ],
        [
            'code' => 'street.1',
            'data' => [
                'required' => "1",
                'length' => "1" // 1 => 50%
            ]
        ]
    ];

    public function __construct(
        private WriterInterface $configWriter,
        private ScopeConfigInterface $scopeConfig
    ){
        parent::__construct(self::COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Change default setting of Hyva to Dutch form setup.');

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->changeStreetLabels();
            $this->changeFieldLength();
        }
        catch (LocalizedException) {
            return 0;
        }

        return 1;
    }

    protected function changeStreetLabels(): void
    {
        foreach ($this->settingChanges as $change) {
            $this->configWriter->save(
                $change['path'],
                $change['value']
            );
        }
    }

    protected function changeFieldLength(): void
    {
        $value      = $this->scopeConfig->getValue(self::EAV_ATTRIBUTE_FORM_FIELD_PATH);
        $attributes = json_decode($value, true);

        if (!$attributes){
            return;
        }

        foreach ($attributes as $key => $attribute){
            $change = $this->getAttributeChange($attribute['attribute_code']);

            if (!$change) {
                continue;
            }

            $attributes[$key] = [
                ...$attribute,
                ...$change['data']
            ];
        }

        $this->configWriter->save(
            self::EAV_ATTRIBUTE_FORM_FIELD_PATH,
            json_encode($attributes)
        );
    }

    protected function getAttributeChange(string $code): ?array
    {
        return array_reduce(
            $this->attributeChanges,
            function ($carry, $change) use ($code) {
                return ($change['code'] === $code) ? $change : $carry;
            },
            null,
        );
    }
}
