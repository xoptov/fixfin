<?php

namespace PerfectMoneyBundle\Tests\Form\EventListener;

use PerfectMoneyBundle\Form\EventListener\V2HashValidationSubscriber;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class V2HashValidationSubscriberTest extends TestCase
{


    public function testPreSubmit()
    {
        /**
         * Мокаем форму
         */
        $form = $this->getMockBuilder('Symfony\\Component\\Form\\Form')
            ->disableOriginalConstructor()
            ->setMethods(['isRoot'])
            ->getMock();

        $form->expects($this->any())->method('isRoot')->willReturn(true);

        /**
         * Фикстуры отправляемых данных в форму
         */
        $submittedData = array(
            'PAYMENT_ID'        => '1',
            'PAYEE_ACCOUNT'     => 'U10006984',
            'PAYMENT_AMOUNT'    => '0.3',
            'PAYMENT_UNITS'     => 'USD',
            'PAYMENT_BATCH_NUM' => '120021117',
            'PAYER_ACCOUNT'     => 'U1178220',
            'TIMESTAMPGMT'      => '1455468796',
            'V2_HASH'           => 'B1C616C485982A36EB796981563A8AFC',
            'BAGGAGE_FIELDS'    => ''
        );


        /**
         * Мокаем событие формы
         */
        $formEvent = $this->getMockBuilder('Symfony\\Component\\Form\\FormEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $formEvent->expects($this->any())->method('getForm')->willReturn($form);
        $formEvent->expects($this->any())->method('getData')->willReturn($submittedData);

        /**
         * Мокаем провайдер токена
         */
        $provider = $this->getMockBuilder('PerfectMoneyBundle\\Service\\TokenProvider')
            ->disableOriginalConstructor()
            ->getMock();
        $provider->expects($this->any())->method('getToken')->will($this->returnValue('46dfa020b4dae4ad25d369db2c213d1d'));

        $V2HashValidationSubscriber = new V2HashValidationSubscriber($provider, new PropertyAccessor());

        $this->assertTrue($V2HashValidationSubscriber->preSubmit($formEvent));
    }
}