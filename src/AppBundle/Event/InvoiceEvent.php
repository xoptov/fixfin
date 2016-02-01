<?php

namespace AppBundle\Event;

use AppBundle\Entity\Invoice;
use Symfony\Component\EventDispatcher\Event;

class InvoiceEvent extends Event
{
    const CREATED = 'invoice.created';
    const EXPIRED = 'invoice.expired';

    /** @var Invoice */
    private $invoice;

    /**
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}