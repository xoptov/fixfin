<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Account;

class LoadUserData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $admin */
        $admin = $userManager->createUser();
        $admin->setUsername('admin')
            ->setEmail('admin@finfix.dev')
            ->setSuperAdmin(true)
            ->setPlainPassword('123456')
            ->setEnabled(true);
        $userManager->updateCanonicalFields($admin);
        $userManager->updatePassword($admin);
        $this->setReference('admin', $admin);
        $manager->persist($admin);

        /** @var User $user1 */
        $user1 = $userManager->createUser();
        $user1->setUsername('xoptov')
            ->setEmail('xoptov@finfix.dev')
            ->setPlainPassword('123456')
            ->setEnabled(true)
            ->setAccount($this->getReference('account.1'));
        $userManager->updateCanonicalFields($user1);
        $userManager->updatePassword($user1);
        $this->setReference('user.1', $user1);
        $manager->persist($user1);

        /** @var User $user2 */
        $user2 = $userManager->createUser();
        $user2->setUsername('kostan')
            ->setEmail('kostan@finfix.dev')
            ->setPlainPassword('123456')
            ->setEnabled(true)
            ->setAccount($this->getReference('account.2'));
        $userManager->updateCanonicalFields($user2);
        $userManager->updatePassword($user2);
        $this->setReference('user.2', $user2);
        $manager->persist($user2);

        /** @var User $user3 */
        $user3 = $userManager->createUser();
        $user3->setUsername('alex')
            ->setEmail('alex@finfix.dev')
            ->setPlainPassword('123456')
            ->setEnabled(true)
            ->setAccount($this->getReference('account.3'));
        $userManager->updateCanonicalFields($user3);
        $userManager->updatePassword($user3);
        $this->setReference('user.3', $user3);
        $manager->persist($user3);

        /** @var User $user4 */
        $user4 = $userManager->createUser();
        $user4->setUsername('oleg')
            ->setEmail('oleg@finfix.dev')
            ->setPlainPassword('123456')
            ->setReferrer($user2)
            ->setEnabled(true)
            ->setAccount($this->getReference('account.4'));
        $userManager->updateCanonicalFields($user4);
        $userManager->updatePassword($user4);
        $this->setReference('user.4', $user4);
        $manager->persist($user4);

        /** @var User $user5 */
        $user5 = $userManager->createUser();
        $user5->setUsername('tanya')
            ->setEmail('tanya@finfix.dev')
            ->setPlainPassword('123456')
            ->setReferrer($user2)
            ->setEnabled(true)
            ->setAccount($this->getReference('account.5'));
        $userManager->updateCanonicalFields($user5);
        $userManager->updatePassword($user5);
        $this->setReference('user.5', $user5);
        $manager->persist($user5);

        /** @var User $user6 */
        $user6 = $userManager->createUser();
        $user6->setUsername('sveta')
            ->setEmail('sveta@finfix.dev')
            ->setPlainPassword('123456')
            ->setReferrer($user3)
            ->setEnabled(true)
            ->setAccount($this->getReference('account.6'));
        $userManager->updateCanonicalFields($user6);
        $userManager->updatePassword($user6);
        $this->setReference('user.6', $user6);
        $manager->persist($user6);

        /** @var User $user7 */
        $user7 = $userManager->createUser();
        $user7->setUsername('john')
            ->setEmail('john@finfix.dev')
            ->setPlainPassword('123456')
            ->setReferrer($user3)
            ->setEnabled(true)
            ->setAccount($this->getReference('account.7'));
        $userManager->updateCanonicalFields($user7);
        $userManager->updatePassword($user7);
        $this->setReference('user.7', $user7);
        $manager->persist($user7);

        /** @var User $user8 */
        $user8 = $userManager->createUser();
        $user8->setUsername('dasha')
            ->setEmail('dasha@finfix.dev')
            ->setPlainPassword('123456')
            ->setReferrer($user7)
            ->setEnabled(true);
        $userManager->updateCanonicalFields($user8);
        $userManager->updatePassword($user8);
        $this->setReference('user.8', $user8);
        $manager->persist($user8);

        /** @var User $user9 */
        $user9 = $userManager->createUser();
        $user9->setUsername('masha')
            ->setEmail('masha@finfix.dev')
            ->setPlainPassword('123456')
            ->setReferrer($user8)
            ->setEnabled(true);
        $userManager->updateCanonicalFields($user9);
        $userManager->updatePassword($user9);
        $this->setReference('user.9', $user9);
        $manager->persist($user9);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            LoadAccountData::class
        );
    }
}