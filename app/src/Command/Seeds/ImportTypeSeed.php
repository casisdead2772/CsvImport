<?php

namespace App\Command\Seeds;

use Doctrine\DBAL\Exception;
use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\ImportType;

class ImportTypeSeed extends Seed {
    protected function configure() {
        $this->setSeedName('ImportTypeSeed');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function load(InputInterface $input, OutputInterface $output): int {
        $this->disableDoctrineLogging();

        $importTypes = [
            [
                'name' => 'Product',
            ],
        ];

        foreach ($importTypes as $importType) {
            $newImportType = new ImportType();
            $newImportType->setName($importType['name']);

            $this->manager->persist($newImportType);
        }
        $this->manager->flush();
        $this->manager->clear();

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws Exception
     */
    public function unload(InputInterface $input, OutputInterface $output): int {
        $this->manager->getConnection()->executeQuery('DELETE FROM import_type');

        return 0; //Must return an exit code
    }
}
