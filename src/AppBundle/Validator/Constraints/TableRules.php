<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class TableRules extends Constraint
{
    public $message = 'Нарушены правила открытия стола, необходимо открыть предидущий стол!';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'table_rules_validator';
    }
}