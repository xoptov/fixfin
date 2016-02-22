<?php

namespace AppBundle\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Psr7\Response;

//TODO: По хорошему это необходимо перенести в тесты.
class TestPoolCommand extends Command
{
    protected function configure()
    {
        $this->setName('test:guzzle:pool');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formData = array(
            'AccountID' => '163830',
            'PassPhrase' => 'maks63813',
            'Account' => 'U10006984'
        );

        $body = http_build_query($formData);

        //TODO: ебать колотить вот в чем причина была! Надо было указать Content-Type
        $request = new Request('post', 'https://perfectmoney.is/acct/acc_name.asp', array('Content-Type' => 'application/x-www-form-urlencoded'), $body);

        $client = new Client();
        $pool = new Pool($client, $request, [
            'fulfilled' => function(Response $response) {
                $this->processSuccessfulResponse($response);
            },
            'rejected' => function($reason) {
                $this->processFailedReason($reason);
            }
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $output->writeln('Hello world!');
    }

    private function processSuccessfulResponse(Response $response)
    {
        return $response;
    }

    private function processFailedReason($reason)
    {
        return $reason;
    }
}