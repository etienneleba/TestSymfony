<?php

namespace App\Tests\Validator;

use App\Repository\ConfigRepository;
use App\Validator\EmailDomain;
use App\Validator\EmailDomainValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @internal
 * @coversNothing
 */
class EmailDomainValidatorTest extends TestCase
{
    public function getValidator($expectedVilation = false, $dbBlockedDomain = [])
    {
        $repository = $this->getMockBuilder(ConfigRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repository
            ->expects($this->any())
            ->method('getAsArray')
            ->willReturn($dbBlockedDomain)
        ;

        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();

        $validator = new EmailDomainValidator($repository);
        if ($expectedVilation) {
            $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
            $violation
                ->expects($this->any())
                ->method('setParameter')
                ->willReturn($violation)
            ;
            $violation
                ->expects($this->once())
                ->method('addViolation')
            ;
            $context
                ->expects($this->once())
                ->method('buildViolation')
                ->willReturn($violation)
            ;
        } else {
            $context
                ->expects($this->never())
                ->method('buildViolation')
            ;
        }

        $validator->initialize($context);

        return $validator;
    }

    public function testCatchBadDomains()
    {
        $constraint = new EmailDomain([
            'blocked' => ['baddomain.fr', 'aze.com'],
        ]);
        $this->getValidator(true)->validate('demo@baddomain.fr', $constraint);
    }

    public function testAcceptGoodDomains()
    {
        $constraint = new EmailDomain([
            'blocked' => ['baddomain.fr', 'aze.com'],
        ]);
        $this->getValidator(false)->validate('demo@gooddomain.fr', $constraint);
    }

    public function testBlockedDomainFromDatabase()
    {
        $constraint = new EmailDomain([
            'blocked' => ['baddomain.fr', 'aze.com'],
        ]);
        $this->getValidator(true, ['baddbdomain.fr'])->validate('demo@baddbdomain.fr', $constraint);
    }
}
