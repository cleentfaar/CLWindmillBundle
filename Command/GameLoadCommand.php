<?php

namespace CL\Bundle\WindmillBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GameLoadCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('windmill:game:load');
        $this->setDescription('Loads an existing chess game using the Windmill engine');
        $this->addArgument('id', InputOption::VALUE_REQUIRED, 'The ID of the game that you wish to continue.');
        $this->addOption('storage', null, InputOption::VALUE_REQUIRED, 'The type of storage that was used to save the game.', 'orm');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->isInteractive() !== true) {
            throw new \LogicException("Can't play chess without interaction");
        }
        $this->outputWelcome($output);
        $id      = $input->getArgument('id');
        $storage = $input->getOption('storage');
        $game    = $this->getStorageHelper()->loadGame($id, $storage);

        if ($game === null) {
            throw new \InvalidArgumentException(sprintf(
                'There is no game with that ID: %s (using storage: %s)',
                $id,
                $storage
            ));
        }

        return $this->playGame($game, $storage, $input, $output);
    }
}
