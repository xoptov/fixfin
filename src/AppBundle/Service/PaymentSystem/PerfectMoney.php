<?php

namespace AppBundle\Service\PaymentSystem;

use AppBundle\Entity\MoneyTransaction;
use GuzzleHttp\Pool as RequestsPool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PerfectMoneyBundle\Model\PaymentRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Pool as AccountPool;
use GuzzleHttp\Client;
use PerfectMoneyBundle\Parser\ResponseParser;
use PerfectMoneyBundle\Model\TransferResponse;
use Monolog\Logger;

class PerfectMoney
{
    /** @var PropertyAccessor */
    private $accessor;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var array */
    private $options;

    /** @var array */
    private $successful;

    /** @var array */
    private $failed;

    /** @var ResponseParser */
    private $responseParser;

    /** @var Client */
    private $client;

    /** @var Logger */
    private $logger;

    /** @var array */
    private $map;

    public function __construct(PropertyAccessor $accessor, EntityManagerInterface $entityManager, array $options, Logger $logger)
    {
        $this->accessor = $accessor;
        $this->entityManager = $entityManager;
        $this->successful = array();
        $this->failed = array();
        $this->responseParser = new ResponseParser();
        $this->client = new Client(array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            )
        ));
        $this->logger = $logger;

        $this->map = array(
            'Payee_Account_Name' => 'payeeAccountName',
            'Payee_Account' => 'payeeAccount',
            'Payer_Account' => 'payerAccount',
            'PAYMENT_AMOUNT' => 'paymentAmount',
            'PAYMENT_BATCH_NUM' => 'paymentBatchNum',
            'PAYMENT_ID' => 'paymentId'
        );

        // Настройка опций для сервиса
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @param Invoice $invoice
     * @return PaymentRequest
     */
    public function createPaymentRequest(Invoice $invoice)
    {
        $request = new PaymentRequest();
        $request->setPaymentId($invoice->getId())
            ->setPaymentAmount($invoice->getAmount())
            ->setPayeeName($this->options['payee_name'])
            ->setPaymentId($invoice->getId())
            ->setPaymentUnits($this->options['payment_units'])
            ->setStatusUrl($this->options['status_url'])
            ->setPaymentUrl($this->options['payment_url'])
            ->setNoPaymentUrl($this->options['nopayment_url'])
            ->setAvailablePaymentMethods($this->options['available_payment_methods']);

        $pool = $this->accessor->getValue($invoice, 'ticket.rate.pool');

        if (!$pool instanceof AccountPool) {
            throw new AccessException();
        }

        $account = $this->entityManager->getRepository('AppBundle:Account')
            ->getPoorestSystemAccount($pool);

        $request->setPayeeAccount($account);

        return $request;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'payee_name' => 'FixFin ltd.',
            'payment_units' => 'USD'
        ));

        $resolver->setRequired(array('entry_form', 'transfer_url', 'status_url', 'payment_url', 'nopayment_url', 'available_payment_methods'));
    }

    /**
     * @param MoneyTransaction $transaction
     * @return Request
     */
    public function createFormParams(MoneyTransaction $transaction)
    {
        $data = array(
            'AccountID' => $this->accessor->getValue($transaction, 'source.login'),
            'PassPhrase' => $this->accessor->getValue($transaction, 'source.password'),
            'Payer_Account' => $this->accessor->getValue($transaction, 'source.number'),
            'Payee_Account' => $this->accessor->getValue($transaction, 'destination.number'),
            'Amount' => $this->accessor->getValue($transaction, 'amount'),
            'Memo' => $this->accessor->getValue($transaction, 'note'),
            'PAYMENT_ID' => $this->accessor->getValue($transaction, 'id')
        );

        return $data;
    }

    /**
     * @param $transactions
     * @throws \Exception
     */
    public function makeTransfers($transactions)
    {
        $requests = array();
        foreach ($transactions as $transaction) {
            $body = http_build_query($this->createFormParams($transaction));
            $requests[] = new Request('POST', $this->options['transfer_url'], array(), $body);
        }

        $pool = new RequestsPool($this->client, $requests, array(
            'pool_size' => 5,
            'fulfilled' => function($response) {
                $this->onSuccessfulResponse($response);
            },
            'rejected' => function($reason) {
                $this->onFailedReason($reason);
            }
        ));

        $pool->promise()->wait();
    }

    /**
     * @param Response $response
     * @return bool
     */
    private function onSuccessfulResponse(Response $response)
    {
        $parser = $this->responseParser;

        /** @var TransferResponse $transfer */
        $transfer = $parser((string)$response->getBody(), $this->map, TransferResponse::class);

        if ($transfer->getError()) {
            // Добавляем идентификатор транзакции и текст ошибки
            $this->failed[$transfer->getPaymentId()] = $transfer->getError();

            return false;
        }
        // Добавляем идентификатор внутренней транзакции и нидентификатор внешней транзакции
        $this->successful[$transfer->getPaymentId()] = $transfer->getPaymentBatchNum();

        return true;
    }

    /**
     * @param Response $response
     */
    private function onFailedReason(Response $response)
    {
        $this->logger->error((string)$response->getBody());
    }

    /**
     * @return array
     */
    public function getSuccessful()
    {
        return $this->successful;
    }

    /**
     * @return array
     */
    public function getFailed()
    {
        return $this->failed;
    }
}