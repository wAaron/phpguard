<?php

namespace spec\PhpGuard\Application;

use PhpGuard\Application\Configuration;
use \PhpGuard\Application\Container\ContainerInterface;
use PhpGuard\Application\PhpGuard;
use PhpGuard\Application\ApplicationEvents;
use PhpGuard\Listen\Event\ChangeSetEvent;
use PhpGuard\Application\Spec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PhpGuardSpec extends ObjectBehavior
{
    static $cwd;

    function let(
        ContainerInterface $container,
        ConsoleOutput $output,
        OutputFormatter $formatter,
        EventDispatcherInterface $dispatcher
    )
    {
        $container->get('ui.output')
            ->willReturn($output);
        $container->get('dispatcher')
            ->willReturn($dispatcher)
        ;
        $output->getVerbosity()
            ->willReturn(ConsoleOutput::VERBOSITY_NORMAL);

        $this->setContainer($container);
        $output->writeln('')
            ->willReturn(null);

        if(!is_dir(self::$cwd)){
            self::$cwd = getcwd();
        }
    }

    function letgo()
    {
        chdir(self::$cwd);
        self::cleanDir(self::$tmpDir.'/test-config');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PhpGuard\Application\PhpGuard');
    }

    function it_should_set_default_options()
    {
        $this->setOptions(array());
        $options = $this->getOptions();

        $options->shouldHaveKey('ignores');
        $options->shouldHaveKey('latency');
    }

    function it_setup_services(ContainerInterface $container)
    {
        $container->setShared('config',Argument::cetera())
            ->shouldBeCalled();
        $container->setShared('dispatcher',Argument::cetera())
            ->shouldBeCalled();
        $container->setShared('dispatcher.listeners.config',Argument::cetera())
            ->shouldBeCalled();
        $container->setShared('dispatcher.listeners.changeset',Argument::cetera())
            ->shouldBeCalled();

        $container->setShared('logger.handler',Argument::cetera())
            ->shouldBeCalled();
        $container->setShared('logger',Argument::cetera())
            ->shouldBeCalled();

        $container->setShared('listen.listener',Argument::cetera())
            ->shouldBeCalled();
        $container->setShared('listen.adapter',Argument::cetera())
            ->shouldBeCalled();

        $this->setupServices();
    }

    function it_should_listen_properly(
        ContainerInterface $container,
        EventDispatcherInterface $dispatcher,
        ChangeSetEvent $event,
        ConsoleOutput $output
    )
    {
        $event->getFiles()
            ->willReturn(array('some_file'));
        $dispatcher->dispatch(ApplicationEvents::postEvaluate,Argument::cetera())
            ->shouldBeCalled();
        $this->listen($event);
    }

    function it_should_load_configuration(
        ContainerInterface $container,
        Configuration $config,
        EventDispatcherInterface $dispatcher
    )
    {
        self::mkdir($dir = self::$tmpDir.'/test-config');
        chdir($dir);

        $container->get('config')
            ->willReturn($config);

        $dispatcher->dispatch(ApplicationEvents::preLoadConfig,Argument::any())
            ->shouldBeCalled();

        $dispatcher->dispatch(ApplicationEvents::postLoadConfig,Argument::any())
            ->shouldBeCalled();

        $config->compileFile(Argument::containingString('phpguard.yml.dist'))
            ->shouldBeCalled();
        touch($dir.'/phpguard.yml.dist');
        $this->loadConfiguration();

        touch($dir.'/phpguard.yml');
        $config->compileFile(Argument::containingString('phpguard.yml'))
            ->shouldBeCalled();

        $this->loadConfiguration();
    }

    function it_throws_when_configuration_file_not_exists()
    {
        chdir(self::$tmpDir);
        $this->shouldThrow('InvalidArgumentException')
            ->duringLoadConfiguration()
        ;
    }
}