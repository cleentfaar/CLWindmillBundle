<?php

namespace CL\Bundle\WindmillBundle\Command;

use CL\Windmill\Decoration\BoardDecorator;
use CL\Windmill\Decoration\PieceDecorator;
use CL\Windmill\Exception\InvalidMoveException;
use CL\Windmill\Exception\InvalidNotationException;
use CL\Windmill\Model\Board\BoardInterface;
use CL\Windmill\Model\Color;
use CL\Windmill\Model\Move\MoveInterface;
use CL\Windmill\Model\Player\ComputerPlayer;
use CL\Windmill\Model\Player\HumanPlayer;
use CL\Windmill\Util\GameFactory;
use CL\Windmill\Model\Game\GameInterface;
use CL\Windmill\Model\Player\PlayerInterface;
use CL\Windmill\Util\MoveParser;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GameCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('windmill:game');
        $this->setDescription('Play a game of chess using the Windmill engine');
        $this->addOption('id', null, InputOption::VALUE_REQUIRED, 'The ID of an existing game that you wish to continue.');
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
        $id      = $input->getOption('id');
        $storage = $input->getOption('storage');
        if ($id !== null) {
            $game = $this->loadGame($storage, $id);
        } else {
            $game = $this->createGame($output);
        }

        return $this->playGame($game, $storage, $output);
    }

    /**
     * @param GameInterface   $game
     * @param string          $storage
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \CL\Windmill\Exception\InvalidMoveException
     * @throws \Exception
     */
    protected function playGame(GameInterface $game, $storage, OutputInterface $output)
    {
        while ($game->hasFinished() !== true) {
            if ($game->getCurrentPlayer()->isHuman()) {
                $this->outputBoard($game->getBoard(), $output);
                $humanMove = $this->askForMove($game->getCurrentPlayer(), $game->getBoard(), $output);
                try {
                    $game->doMove($humanMove);
                } catch (InvalidMoveException $e) {
                    $output->writeln(sprintf('<error>Invalid move: %s</error>', $e->getMessage()));

                    return $this->playGame($game, $storage, $output);
                }
            } else {
                try {
                    $game->doEngineMove();
                } catch (InvalidMoveException $e) {
                    $output->writeln('<error>Invalid move by computer, this should not have happened!</error>');

                    throw $e;
                }
            }
            $this->saveGame($storage, $game);
        }

        return 0;
    }

    /**
     * @param string     $storage
     * @param string|int $id
     *
     * @return \CL\Windmill\Model\Game\Game
     */
    protected function loadGame($storage, $id)
    {
        return $game = $this->getGameFactory()->load($storage, $id);
    }

    /**
     * @param OutputInterface $output
     *
     * @return \CL\Windmill\Model\Game\Game
     */
    protected function createGame(OutputInterface $output)
    {
        $whitePlayer = $this->askForPlayerSetup(Color::WHITE, $output);
        $blackPlayer = $this->askForPlayerSetup(Color::BLACK, $output);
        $game        = $this->getGameFactory()->create($whitePlayer, $blackPlayer);

        return $game;
    }

    /**
     * @param string        $storageType
     * @param GameInterface $game
     */
    protected function saveGame($storageType, GameInterface $game)
    {
        $this->getGameFactory()->save($storageType, $game);
    }

    /**
     * @param PlayerInterface $player
     * @param BoardInterface  $board
     * @param OutputInterface $output
     *
     * @return MoveInterface
     */
    protected function askForMove(PlayerInterface $player, BoardInterface $board, OutputInterface $output)
    {
        $output->writeln(sprintf('<comment>%s\'s turn to move.</comment>', $player->getName()));
        $moveNotation = $this->getDialogHelper()->ask($output, '<question>Please enter the position to move to:</question> ');
        try {
            $move = $this->getMoveParser()->parse($moveNotation, $board, $player->getColor());
        } catch (InvalidNotationException $e) {
            $output->write(sprintf('<error>Invalid notation: %s</error>', $e->getMessage()));

            return $this->askForMove($player, $board, $output);
        }

        return $move;
    }

    /**
     * @param BoardInterface  $board
     * @param OutputInterface $output
     */
    protected function outputBoard(BoardInterface $board, OutputInterface $output)
    {
        $boardDecorator = new BoardDecorator(new PieceDecorator());
        $output->writeln($boardDecorator->toAscii($board));
    }

    /**
     * @param int             $color
     * @param OutputInterface $output
     *
     * @return array
     */
    protected function askForPlayerSetup($color, OutputInterface $output)
    {
        $player     = [
            'color' => $color,
        ];
        $colorText  = $player['color'] == Color::WHITE ? 'white' : 'black';
        $playerName = $this->getDialogHelper()->ask(
            $output,
            sprintf('Please enter the name of the player controlling %s: ', $colorText),
            $player['color'] == Color::WHITE ? 'Player 1' : 'Player 2'
        );

        $humanControlledQuestion = '<question>Will this player be human-controlled?</question> ';
        $humanControlled         = $this->getDialogHelper()->askConfirmation($output, $humanControlledQuestion, 'y', ['y', 'N']);
        if ($humanControlled === 'N') {
            $player = new ComputerPlayer($color, $playerName);
        } else {
            $player = new HumanPlayer($color, $playerName);
        }

        return $player;
    }

    /**
     * @return DialogHelper
     */
    protected function getDialogHelper()
    {
        return $this->getHelperSet()->get('dialog');
    }

    /**
     * @return MoveParser
     */
    protected function getMoveParser()
    {
        return $this->getContainer()->get('cl_windmill.util.move_parser');
    }

    /**
     * @return GameFactory
     */
    protected function getGameFactory()
    {
        return $this->getContainer()->get('cl_windmill.util.game_factory');
    }

    /**
     * @param OutputInterface $output
     */
    protected function outputWelcome(OutputInterface $output)
    {
        $output->writeln('###############################################');
        $output->writeln('#                                             #');
        $output->writeln('#    <fg=yellow>Welcome to the Windmill chess engine!</fg=yellow>    #');
        $output->writeln('#                                             #');
        $output->writeln('###############################################');
    }
}
