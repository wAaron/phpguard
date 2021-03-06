<?php

namespace spec\PhpGuard\Application\Event;

use PhpGuard\Application\Event\ResultEvent;
use PhpGuard\Application\Spec\ObjectBehavior;

class ResultEventSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(ResultEvent::SUCCEED);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Application\Event\ResultEvent');
    }

    function it_isSucceed_returns_true_if_command_succeed($plugin)
    {
        $this->beConstructedWith(ResultEvent::SUCCEED);
        $this->shouldBeSucceed();
    }

    function it_isFailed_returns_true_if_command_did_not_succeed($plugin)
    {
        $this->beConstructedWith(ResultEvent::FAILED);
        $this->shouldBeFailed();
    }

    function it_isBroken_returns_true_if_command_did_not_succeed_and_has_error($plugin)
    {
        $this->beConstructedWith(ResultEvent::BROKEN,new \Exception('some'));
        $this->shouldBeBroken();
    }

    function it_should_create_succeed_event()
    {
        $this->createSucceed('Succeed');
        $this->shouldBeSucceed();
    }

    function it_should_create_failed_event()
    {
        $ob = $this->createFailed('Failed');
        $ob->shouldBeFailed();
    }

    function it_should_create_broken_event()
    {
        $ob = $this->createBroken('Broken');
        $ob->shouldBeBroken();
    }

    function it_should_create_error_event()
    {
        $exception = new \Exception('foo bar');
        $ob = $this->createError('Error',array(),$exception );
        $ob->shouldBeError();
        $ob->getException()->shouldReturn($exception);
        $ob->getTrace()->shouldNotReturn(array());

        $trace = array('some_trace');
        $ob = $this->createError('Error',array(),$exception,$trace);
        $ob->getTrace()->shouldContain('some_trace');
    }

    function it_stores_arguments()
    {
        $arguments = array(
            'foo' => 'bar',
            'hello' => 'world',
        );
        $this->beConstructedWith(ResultEvent::SUCCEED,'Succeed',$arguments);
        $this->getArgument('foo')->shouldReturn('bar');
        $this->getArgument('hello')->shouldReturn('world');

        $this->getArguments()->shouldReturn($arguments);
    }
}
