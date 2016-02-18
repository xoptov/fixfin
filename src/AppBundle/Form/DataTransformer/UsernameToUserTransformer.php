<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UsernameToUserTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $value
     * @return string
     */
    public function transform($value)
    {
        if ($value instanceof User) {
            return $value->getUsername();
        }

        return '';
    }

    /**
     * @param string $value
     * @return User
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array('username' => $value));

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}