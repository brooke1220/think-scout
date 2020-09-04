<?php

namespace brooke\scout\commands;

use think\console\Input;
use think\console\Output;
use think\console\Command;
use think\console\input\Argument;

class FlushCommand extends Command
{
    protected function configure()
    {
        $this->setName('scout:flush')
          ->setDescription("Flush all of the model's records from the index")
          ->addArgument('model', Argument::REQUIRED);
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $class = $input->getArgument('model');

        $model = new $class;

        $model::removeAllFromSearch();

        $output->writeln('All ['.$class.'] records have been flushed.');
    }
}
