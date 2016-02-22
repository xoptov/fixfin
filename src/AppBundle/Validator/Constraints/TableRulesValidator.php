<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use AppBundle\Entity\Rate;
use AppBundle\Entity\User;

class TableRulesValidator extends ConstraintValidator
{
    /** @var PropertyAccessor */
    private $accessor;

    public function __construct(PropertyAccessor $accessor)
    {
        $this->accessor = $accessor;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /**
         * @var Rate $rate
         * @var User $user
         * @var TableRules $constraint
         */
        $rate = $this->accessor->getValue($value, 'rate');
        $user = $this->accessor->getValue($value, 'user');

        if ($rate->getRequireScore() && ($rate->getRequireScore() & $user->getScore()) == 0) {
            $this->context->addViolation($constraint->message);
        }
    }
}