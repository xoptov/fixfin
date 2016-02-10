<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BankerMakeRewardsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('banker:make:rewards');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $banker = $this->getContainer()->get('app.banker_service');
        $reserved = $banker->makeRewardPayments();

        $reserved = $reserved ? $reserved : 0;

        $output->writeln('Reserved funds: ' . $reserved);
    }
}