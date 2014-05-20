<?php

/*
 * This file is part of the PhpGuard project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpGuard\Application\Tests;
use PhpGuard\Application\Console\Application;
use PhpGuard\Application\Console\Shell;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestApplication extends Application
{
    public function __construct()
    {
        parent::__construct();
        $this->setCatchExceptions(true);
        $this->setAutoExit(false);

        $container = $this->getContainer();
        $container->setShared('plugins.test',function($c){
            return new TestPlugin();
        });
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $container->set('ui.input',$input);
        $container->set('ui.output',$output);
        $container->set('ui.shell',new TestShell($this->getContainer()));

        $command = $this->getCommandName($input);
        if($command=='evaluate'){
            return $this->getShell()->evaluate();
        }
        return parent::doRun($input,$output);
    }

    /**
     * @return Shell
     */
    public function getShell()
    {
        return $this->getContainer()->get('ui.shell');
    }

    public function evaluate()
    {
        $this->getShell()->evaluate();
    }
} 