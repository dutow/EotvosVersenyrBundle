<?php

namespace Cancellar\TrackerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for displaying the list of the unique visitors of the site. Supports time limits.
 *
 * @author Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @since 0.1
 * @version 0.1
 */
class UniqueVisitorsCommand extends ContainerAwareCommand{

  /**
   * Task configuration.
   */
  protected function configure(){
    parent::configure();

    $this
      ->setName('tracker:uniquevisitors')
      ->setDescription('Displays the list of the unique visitors')
      ->addOption('from', null, InputOption::VALUE_NONE, 'If set, the task will display entries after this date. Format YYYY[-mm[-dd [HH:[MM:[SS]]]]]')
      ->addOption('until', null, InputOption::VALUE_NONE, 'If set, the task will display entries until this date. Format YYYY[-mm[-dd [HH:[MM:[SS]]]]]')
      ;
  }

  /**
   * Task main method.
   */
  protected function execute(InputInterface $input, OutputInterface $output){
    $from = $input->getOption('from');
    $until = $input->getOption('until');

    $entryRepo = $this->getContainer()->get('doctrine')->getRepository('CancellarTrackerBundle:LogEntry');

    $list = $entryRepo->findUniqueVisitors($from, $until);

      $output->writeln('-------------------------------------------------------------------------------------------------------------');
      $output->writeln(sprintf('| %-36s | %-20s | %-20s | %-20s |', 'UUID', 'IP', 'First', 'Last'));
      $output->writeln('-------------------------------------------------------------------------------------------------------------');
    foreach($list as $item){
      $output->writeln(sprintf('| %-36s | %-20s | %-20s | %-20s |', $item['uuid'], $item['ip'], $item['first'], $item['last']));
      $output->writeln('-------------------------------------------------------------------------------------------------------------');
    }
  }

}

