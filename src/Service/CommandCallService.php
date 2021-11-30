<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;

class CommandCallService {
    private KernelInterface $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel) {
        $this->kernel = $kernel;
    }
    /**
     * @param string $fileName
     *
     * @return int
     *
     * @throws \Exception
     */
    public function importCsvDB(string $fileName): int {
        /** @var $application */
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:import',
            'filename' => $fileName,
        ]);
        $output = new BufferedOutput();

        return $application->run($input, $output);
    }
}
