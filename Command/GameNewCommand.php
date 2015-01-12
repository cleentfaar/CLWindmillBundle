<?php

namespace CL\Bundle\WindmillBundle\Command;

use CL\Windmill\Model\Color;
use CL\Windmill\Model\Game\Game;
use CL\Windmill\Model\Player\Player;
use CL\Windmill\Util\GameFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GameNewCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('windmill:game:new');
        $this->setDescription('Start a new chess game using the Windmill engine');
        $this->addOption('storage', null, InputOption::VALUE_REQUIRED, 'The type of storage to use for saving game states.', 'orm');
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
        $storage = $input->getOption('storage');
        $game    = $this->createGame($input, $output);

        return $this->playGame($game, $storage, $input, $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return Game
     */
    protected function createGame(InputInterface $input, OutputInterface $output)
    {
        $whitePlayer = $this->askForPlayer(Color::WHITE, $input, $output);
        $blackPlayer = $this->askForPlayer(Color::BLACK, $input, $output);
        $game        = GameFactory::create($whitePlayer, $blackPlayer);

        $output->writeln(sprintf('Started game with ID <comment>%s</comment>', $game->getId()));

        return $game;
    }

    /**
     * @param int             $color
     * @param OutputInterface $output
     *
     * @return array
     */
    protected function askForPlayer($color, InputInterface $input, OutputInterface $output)
    {
        $player     = [
            'color' => $color,
        ];
        $colorText  = $player['color'] == Color::WHITE ? 'white' : 'black';
        $playerName = $this->ask(
            sprintf('Please enter the name of the player controlling %s: ', $colorText),
            $player['color'] == Color::WHITE ? 'Player 1' : 'Player 2',
            $input,
            $output
        );

        $humanControlledQuestion = '<question>Will this player be human-controlled?</question> ';
        $humanControlled         = $this->askConfirmation($humanControlledQuestion, 'y', $input, $output);

        $player = new Player($color, $playerName, (bool) $humanControlled);

        return $player;
    }
}
