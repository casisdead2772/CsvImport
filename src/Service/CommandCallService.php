<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class CommandCallService {
    /**
     * @param string $fileName
     * @param KernelInterface $kernel
     *
     * @return int
     *
     * @throws \Exception
     */
    public function importCsvDB(string $fileName, KernelInterface $kernel): int {
        /** @var $application */
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:import',
            'filename' => $fileName,
        ]);
        $output = new BufferedOutput();

        return $application->run($input, $output);
    }
}
