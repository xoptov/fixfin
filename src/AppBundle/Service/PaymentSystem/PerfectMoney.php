<?php

namespace AppBundle\Service\PaymentSystem;

use AppBundle\Entity\MoneyTransaction;
use GuzzleHttp\Psr7\Request;
use PerfectMoneyBundle\Model\PaymentRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Pool as RequestsPool;

class PerfectMoney
{
    /** @var PropertyAccessor */
    private $accessor;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var array */
    private $options;

    public function __construct(PropertyAccessor $accessor, EntityManagerInterface $entityManager, array $options)
    {
        $this->accessor = $accessor;
        $this->entityManager = $entityManager;

        // Настройка опций для сервиса
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function createPaymentRequest(Invoice $invoice)
    {
        $request = new PaymentRequest();
        $request->setPaymentId($invoice->getId())
            ->setPaymentAmount($invoice->getAmount())
            ->setPayeeName($this->options['payee_name'])
            ->setPaymentUnits($this->options['payment_units'])
            ->setStatusUrl($this->options['status_url'])
            ->setPaymentUrl($this->options['payment_url'])
            ->setNoPaymentUrl($this->options['nopayment_url'])
            ->setAvailablePaymentMethods($this->options['available_payment_methods']);

        $pool = $this->accessor->getValue($invoice, 'ticket.rate.pool');

        if (!$pool instanceof Pool) {
            throw new AccessException();
        }

        $account = $this->entityManager->getRepository('AppBundle:Account')
            ->getPoorestSystemAccount($pool);

        $request->setPayeeAccount($account);

        return $request;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'payee_name' => 'FixFin ltd.',
            'payment_units' => 'USD'
        ));

        $resolver->setRequired(array('status_url', 'payment_url', 'nopayment_url', 'available_payment_methods'));
    }

    public function createTransferRequest(MoneyTransaction $transaction)
    {
        //TODO: это ёбаный колхоз но я пока незнаю как сделать лучше
        $form = array(
            'AccountID' => $this->accessor->getValue($this->options, '[account_id]'),
            'PassPhrase' => $this->accessor->getValue($this->options, '[pass_phrase]'),
            'Payer_Account' => $this->accessor->getValue($transaction, 'source.number'),
            'Payee_Account' => $this->accessor->getValue($transaction, 'destination.number'),
            'Amount' => $this->accessor->getValue($transaction, 'amount'),
            'Memo' => $this->accessor->getValue($transaction, 'note'),
            'PAYMENT_ID' => $this->accessor->getValue($transaction, 'id')
        );

        return new Request('POST', $this->options['transfer_url'], [], ['form_param' => $form]);
    }

    public function makeTransfers($transactions)
    {
        $requests = array();

        foreach($transactions as $transaction) {
            $requests[] = $this->createTransferRequest($transaction);
        }

        $pool = new RequestsPool(new Client(), $requests, [
            'concurrency' => 5,
            'fulfilled' => function($response, $index){
                //TODO: вообщем здесь доступен $this и нужно перерисовать схему
            },
            'rejected' => function($reason, $index){
                //TODO: тут нужно отмечать неудачные транзакции
            }
        ]);

        $pool->promise()->wait();
    }
}