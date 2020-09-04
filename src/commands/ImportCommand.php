<?php

namespace brooke\scout\commands;


use think\console\Input;
use think\console\Output;
use think\console\Command;
use think\console\input\Argument;
use think\facade\Hook;
use brooke\scout\events\ModelsImported;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this->setName('scout:import')
          ->setDescription('Import the given model into the search index')
          ->addArgument('model', Argument::REQUIRED, 'Class name of model to bulk import')
          ->addArgument('chunk', Argument::OPTIONAL, 'The number of records to import at a time (Defaults to configuration value: `scout.chunk.searchable`)');
    }

     protected function execute(Input $input, Output $output)
     {
          $class = $input->getArgument('model');

          $model = new $class;

          Hook::add(ModelsImported::class, function ($models) use ($class, $output) {
              $key = $models->last()->getScoutKey();
              $output->writeln('<comment>Imported ['.$class.'] models up to ID:</comment> '.$key);
          });

          $model::makeAllSearchable($input->getArgument('chunk'));

          $output->writeln('All ['.$class.'] records have been imported.');
    }
}
