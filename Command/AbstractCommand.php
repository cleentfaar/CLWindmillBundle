<?php

namespace CL\Bundle\WindmillBundle\Command;

use CL\Windmill\Decorator\BoardDecorator;
use CL\Windmill\Decorator\PieceDecorator;
use CL\Windmill\Exception\InvalidMoveException;
use CL\Windmill\Exception\InvalidNotationException;
use CL\Windmill\Model\Board\BoardInterface;
use CL\Windmill\Model\Game\GameInterface;
use CL\Windmill\Model\Move\MoveInterface;
use CL\Windmill\Model\Player\PlayerInterface;
use CL\Windmill\Util\GameFactory;
use CL\Windmill\Util\LazyMoveCalculator;
use CL\Windmill\Util\MoveCalculator;
use CL\Windmill\Util\MoveParser;
use CL\Windmill\Util\StorageHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class AbstractCommand extends ContainerAwareCommand
{

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
    protected function playGame(GameInterface $game, $storage, InputInterface $input, OutputInterface $output)
    {
        while ($game->hasFinished() !== true) {
            if ($game->getCurrentPlayer()->isHuman()) {
                $this->outputBoard($game->getBoard(), $output);
                $humanMove = $this->askForMove($game->getCurrentPlayer(), $game->getBoard(), $input, $output);
                try {
                    $game->doMove($humanMove);
                } catch (InvalidMoveException $e) {
                    $output->writeln(sprintf('<error>Invalid move: %s</error>', $e->getMessage()));

                    return $this->playGame($game, $storage, $input, $output);
                }
            } else {
                try {
                    $game->doEngineMove($this->getMoveCalculator());
                } catch (InvalidMoveException $e) {
                    $output->writeln('<error>Invalid move by computer, this should not have happened!</error>');

                    throw $e;
                }
            }

            $this->getStorageHelper()->saveGame($game, $storage);
        }

        return 0;
    }

    /**
     * @param PlayerInterface $player
     * @param BoardInterface  $board
     * @param OutputInterface $output
     *
     * @return MoveInterface
     */
    protected function askForMove(PlayerInterface $player, BoardInterface $board, InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('<comment>%s\'s turn to move.</comment>', $player->getName()));
        $moveNotation = $this->ask('<question>Please enter the position to move to:</question> ', $input, $output);
        try {
            $move = $this->getMoveParser()->parse($moveNotation, $board, $player->getColor());
        } catch (InvalidNotationException $e) {
            $output->write(sprintf('<error>Invalid notation: %s</error>', $e->getMessage()));

            return $this->askForMove($player, $board, $input, $output);
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
     * @return MoveCalculator|LazyMoveCalculator
     */
    protected function getMoveCalculator()
    {
        return $this->getContainer()->get('cl_windmill.util.move_calculator');
    }

    /**
     * @return StorageHelper
     */
    protected function getStorageHelper()
    {
        return $this->getContainer()->get('cl_windmill.util.storage_helper');
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

    /**
     * @param string          $question
     * @param null            $default
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $autoCompleterValues
     *
     * @return string
     */
    protected function ask($question, $default = null, InputInterface $input, OutputInterface $output, array $autoCompleterValues = [])
    {
        $question = $this->createQuestion($question, $default);
        if (!empty($autoCompleterValues)) {
            $question->setAutocompleterValues($autoCompleterValues);
        }

        return $this->getQuestionHelper()->ask(
            $input,
            $output,
            $question
        );
    }

    /**
     * @param string      $question
     * @param string|null $default
     *
     * @return Question
     */
    protected function createQuestion($question, $default)
    {
        return new Question($question, $default);
    }

    /**
     * @return QuestionHelper
     */
    protected function getQuestionHelper()
    {
        return $this->getHelperSet()->get('question');
    }
}
