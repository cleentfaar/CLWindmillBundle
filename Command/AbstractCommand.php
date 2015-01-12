<?php

namespace CL\Bundle\WindmillBundle\Command;

use CL\Windmill\Decorator\BoardDecorator;
use CL\Windmill\Exception\InvalidMoveException;
use CL\Windmill\Exception\InvalidNotationException;
use CL\Windmill\Model\Board\BoardInterface;
use CL\Windmill\Model\Game\GameInterface;
use CL\Windmill\Model\Player\PlayerInterface;
use CL\Windmill\Util\MoveCalculator;
use CL\Windmill\Util\MoveRanker;
use CL\Windmill\Util\NotationParser;
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
        while ($game->hasFinished() === false) {
            $output->writeln('');
            $this->outputBoard($game->getBoard(), $output);
            if ($game->getCurrentPlayer()->isHuman()) {
                list($from, $to) = $this->askForMove($game->getCurrentPlayer(), $game, $input, $output);
                try {
                    $moveMade = $game->doHumanMove($from, $to);
                    $moveMade->setRanking(MoveRanker::rank($moveMade, $game, $this->getMoveCalculator()));
                } catch (InvalidMoveException $e) {
                    $output->writeln(sprintf('<error>Invalid move: %s</error>', $e->getMessage()));

                    return $this->playGame($game, $storage, $input, $output);
                }
            } else {
                $moveMade = $game->doEngineMove($this->getMoveCalculator());
            }

            $game->finishTurn($this->getMoveCalculator());

            $this->getStorageHelper()->saveGame($game, $storage);

            $piece = $moveMade->getPiece();

            $output->writeln(sprintf(
                'Move made by <comment>%s</comment>: <comment>%s</comment> from <comment>%s</comment> to <comment>%s</comment>',
                $game->getOpposingPlayer()->getName(),
                $piece->getTypeLabel(),
                $moveMade->getFromLabel(),
                $moveMade->getToLabel()
            ));

            if ($moveMade->getRanking() !== null) {
                $output->writeln(sprintf('Windmill ranked this move at: <comment>%d</comment>', $moveMade->getRanking()));
            }
        }

        return 0;
    }

    /**
     * @param PlayerInterface $player
     * @param GameInterface   $game
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return array
     */
    protected function askForMove(PlayerInterface $player, GameInterface $game, InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('<comment>%s\'s turn to move.</comment>', $player->getName()));
        $moveNotation = $this->ask('<question>Please enter the position to move to:</question> ', null, $input, $output);
        try {
            $parsed = $this->getNotationParser()->parse($moveNotation, $game, $player->getColor());

            return [$parsed['from'], $parsed['to']];
        } catch (InvalidNotationException $e) {
            $output->write(sprintf('<error>Invalid notation: %s</error>', $e->getMessage()));

            return $this->askForMove($player, $game, $input, $output);
        }
    }

    /**
     * @param BoardInterface  $board
     * @param OutputInterface $output
     */
    protected function outputBoard(BoardInterface $board, OutputInterface $output)
    {
        $output->writeln(BoardDecorator::toAscii($board));
    }

    /**
     * @return MoveCalculator
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
     * @return NotationParser
     */
    protected function getNotationParser()
    {
        return $this->getContainer()->get('cl_windmill.util.notation_parser');
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
     * @param string          $question
     * @param bool            $default
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function askConfirmation($question, $default = true, InputInterface $input, OutputInterface $output)
    {
        $answer = 'z';
        while ($answer && !in_array(strtolower($answer[0]), array('y', 'n'))) {
            $answer = $this->ask($question, $default, $input, $output);
        }

        if (false === $default) {
            return $answer && 'y' == strtolower($answer[0]);
        }

        return !$answer || 'y' == strtolower($answer[0]);
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
