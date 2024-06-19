<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\ZipcodeChecker\Console\Command;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;


class SetupDutchFields extends Command
{
    protected array $changes = [
        [
            'path' => 'hyva_themes_checkout/address_form/street/field_label_0',
            'value' => 'Street'
        ],
        [
            'path' => 'hyva_themes_checkout/address_form/street/field_label_1',
            'value' => 'Housenumber'
        ]
    ];

    public function __construct(
        private WriterInterface $configWriter,
        ?string $name = null
    ){
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('elgentos:checkout:setup-dutch-fields');
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

            foreach ($this->changes as $change) {
                $this->configWriter->save(
                    $change['path'],
                    $change['value'],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    $scopeId = 0
                );
            }
        }
        catch (Exception) {
            return 0;
        }


        return 1;
    }


}
