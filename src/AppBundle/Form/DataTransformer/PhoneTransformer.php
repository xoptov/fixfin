<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PhoneTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null == $value) {
            return '';
        }

        $value = preg_replace('/^(\d)(\d{3})(\d{3})(\d{4})/', '+$1($2)$3-$4', $value);

        return $value;
    }

    public function reverseTransform($value)
    {
        if (null == $value) {
            return null;
        }

        $patterns = array('/[^\d]+/', '/^8/');
        $replacement = array('', '7');
        $value = preg_replace($patterns, $replacement, $value);

        return $value;
    }
}