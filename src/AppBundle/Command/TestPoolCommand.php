<?php

namespace AppBundle\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestPoolCommand extends Command
{
    protected function configure()
    {
        $this->setName('test:guzzle:pool');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new Request('GET', 'http://www.pozari.ru');

        $pool = new Pool(new Client(), [$request], [
            'fulfilled' => function($response, $index) {
                return $this;
            },
            'rejected' => function($reason, $index) {
                return $this;
            }
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }
}