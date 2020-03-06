<?php

namespace App\Tests\Entity;

use App\Entity\InvitationCode;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 * @coversNothing
 */
class InvitationCodeTest extends KernelTestCase
{
    use FixturesTrait;

    public function getEntity(): InvitationCode
    {
        return $code = (new InvitationCode())
            ->setCode('12345')
            ->setDescription('Description de test')
            ->setExpireAt(new \DateTime())
        ;
    }

    public function assertAsErrors(InvitationCode $code, int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($code);
        // pour compter le nombre d'erreur on utilise assertCount
        $this->assertCount($number, $error);
    }

    public function testValidCodeEntity()
    {
        $this->assertAsErrors($this->getEntity(), 0);
    }

    public function testInvalidCodeEntity()
    {
        $this->assertAsErrors($this->getEntity()->setCode('1a345'), 1);
        $this->assertAsErrors($this->getEntity()->setCode('1234'), 1);
    }

    public function testInvalidBlankCodeEntity()
    {
        $this->assertAsErrors($this->getEntity()->setCode(''), 1);
    }

    public function testInvalidBlankDescriptionEntity()
    {
        $this->assertAsErrors($this->getEntity()->setDescription(''), 1);
    }

    public function testInvalidUsedCodeEntity()
    {
        $this->loadFixtureFiles([__DIR__.'/InvitationCodeTestFixtures.yaml']);
        $this->assertAsErrors($this->getEntity()->setCode('54321'), 1);
    }
}
