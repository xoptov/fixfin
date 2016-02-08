<?php

namespace PerfectMoneyBundle\Parser;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ResponseParser
{
    /**
     * @param string $content   Контент для парсинга.
     * @param array  $map       Массив ассоциаций.
     * @param string $class     Класс для объекта который будет заполнятся.
     * @return mixed            Объект ответа PerfectMoney.
     */
    public function __invoke($content, array $map, $class)
    {
        $response = new $class();
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        // Ищем ошибки в ответе PerfectMoney
        $inputError = $crawler->filter('html > body > input[name=ERROR]');
        if ($inputError->count()) {
            return $response->setError($inputError->attr('value'));
        }

        $propertyAccessor = new PropertyAccessor();

        try {
            $crawler->filter('html > body > input')
                ->each(function($node) use (&$map, &$propertyAccessor, &$response){
                    $propertyAccessor->setValue($response, $map[$node->attr('name')], $node->attr('value'));
                });
        } catch (\Exception $e) {
            return $response->setError($e->getMessage());
        }

        return $response;
    }
}