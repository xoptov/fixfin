<?php

namespace PerfectMoneyBundle\Tests\Handler;

use PerfectMoneyBundle\Parser\ResponseParser;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use PerfectMoneyBundle\Model\SpentResponse;

class SpentResponseHandlerTest extends TestCase
{
    /** @var array */
    private $map;

    public function setUp()
    {
        // Массив соотношений полей
        $this->map = array(
            'Payee_Account_Name' => 'payeeAccountName',
            'Payee_Account' => 'payeeAccount',
            'Payer_Account' => 'payerAccount',
            'PAYMENT_AMOUNT' => 'paymentAmount',
            'PAYMENT_BATCH_NUM' => 'paymentBatchNum',
            'PAYMENT_ID' => 'paymentId'
        );
    }

    public function testParseSuccess()
    {
        $content = <<<EOF
<html>

<head>
  <title>Spend</title>
</head>

<body>

<h1>Spend</h1>

<table border=1>
<tr><td><b>Name</b></td><td><b>Value</b></td></tr><tr><td>Payee_Account_Name</td><td>FixFin Inc</td></tr>
<tr><td>Payee_Account</td><td>U10006984</td></tr>
<tr><td>Payer_Account</td><td>U1178220</td></tr>
<tr><td>PAYMENT_AMOUNT</td><td>0.01</td></tr>
<tr><td>PAYMENT_BATCH_NUM</td><td>119341943</td></tr>
<tr><td>PAYMENT_ID</td><td>1</td></tr>
</table>
<input name='Payee_Account_Name' type='hidden' value='FixFin Inc'>
<input name='Payee_Account' type='hidden' value='U10006984'>
<input name='Payer_Account' type='hidden' value='U1178220'>
<input name='PAYMENT_AMOUNT' type='hidden' value='0.01'>
<input name='PAYMENT_BATCH_NUM' type='hidden' value='119341943'>
<input name='PAYMENT_ID' type='hidden' value='1'>
</body>

</html>
EOF;
        $parser = new ResponseParser();
        /** @var SpentResponse $result */
        $result = $parser($content, $this->map, SpentResponse::class);

        $this->assertNotInternalType('string', $result);
        $this->assertInstanceOf('PerfectMoneyBundle\\Model\\SpentResponse', $result);
        $this->assertNull($result->getError());
        $this->assertEquals($result->getPayeeAccountName(), 'FixFin Inc');
        $this->assertEquals($result->getPayeeAccount(), 'U10006984');
        $this->assertEquals($result->getPayerAccount(), 'U1178220');
        $this->assertEquals($result->getPaymentAmount(), 0.01);
        $this->assertEquals($result->getPaymentBatchNum(), 119341943);
        $this->assertEquals($result->getPaymentId(), 1);
    }

    public function testParseError()
    {
        $content = <<<EOF
<html>

<head>
  <title>Spend</title>
</head>

<body>

<h1>Spend</h1>
Error: Can not login with passed AccountID and PassPhrase
<input name='ERROR' type='hidden' value='Can not login with passed AccountID and PassPhrase'>
</body>

</html>
EOF;
        $parser = new ResponseParser();
        /** @var SpentResponse $result */
        $result = $parser($content, $this->map, SpentResponse::class);

        $this->assertInstanceOf('PerfectMoneyBundle\\Model\\SpentResponse', $result);
        $this->assertNotEmpty($result->getError());
        $this->assertNull($result->getPayeeAccountName());
        $this->assertNull($result->getPayeeAccount());
        $this->assertNull($result->getPayerAccount());
        $this->assertNull($result->getPaymentAmount());
        $this->assertNull($result->getPaymentBatchNum());
        $this->assertNull($result->getPaymentId());
    }
}