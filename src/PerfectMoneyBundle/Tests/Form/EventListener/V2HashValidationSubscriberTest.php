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
            'PAYMENT_ID'        => '19821111',
            'PAYEE_ACCOUNT'     => 'U9102389',
            'PAYMENT_AMOUNT'    => '20.00',
            'PAYMENT_UNITS'     => 'USD',
            'PAYMENT_BATCH_NUM' => '20171105',
            'PAYER_ACCOUNT'     => 'U2482738',
            'TIMESTAMPGMT'      => '1454666616',
            'V2_HASH'           => '2560071A5A9D9BE236596FF4B68D8467'
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
        $provider->expects($this->any())->method('getToken')->will($this->returnValue('4EA5DD31EE8EA77F8714D66479D00902'));

        $V2HashValidationSubscriber = new V2HashValidationSubscriber($provider, new PropertyAccessor());

        $this->assertTrue($V2HashValidationSubscriber->preSubmit($formEvent));
    }
}