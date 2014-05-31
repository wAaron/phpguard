<?php

namespace spec\PhpGuard\Application\Event;

use PhpGuard\Application\Container\ContainerInterface;
use PhpGuard\Application\PhpGuard;
use PhpGuard\Application\Spec\ObjectBehavior;

class GenericEventSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container,PhpGuard $phpGuard)
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Application\Event\GenericEvent');
    }

    public function it_should_extends_the_Symfony_Event()
    {
        $this->shouldHaveType('\Symfony\Component\EventDispatcher\Event');
    }

    public function its_getContainer_returns_the_container_object($container)
    {
        $this->getContainer()->shouldReturn($container);
    }
}
