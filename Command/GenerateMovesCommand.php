<?php

namespace CL\Bundle\WindmillBundle\Command;

use CL\Windmill\Decorator\PieceDecorator;
use CL\Windmill\Model\Color;
use CL\Windmill\Model\Move\Move;
use CL\Windmill\Model\Piece\PieceInterface;
use CL\Windmill\Util\BoardFactory;
use CL\Windmill\Util\BoardHelper;
use CL\Windmill\Util\MoveCalculator;
use CL\Windmill\Util\MoveRegistry;
use CL\Windmill\Util\PieceFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMovesCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('windmill:generate:moves');
        $this->setDescription('Generates all possible moves for all possible pieces from all possible positions; saving on calculation time later');
        $this->addArgument('method', InputArgument::OPTIONAL, 'The method to use for generating the data', MoveRegistry::STORAGE_JSON);
        $this->addOption('target', 't', InputOption::VALUE_REQUIRED, 'The target to use for this method (e.g. path to file)', './moves.json');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Whether existing entries should be overwritten (for instance: to fix existing issues)');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pieceTypes = [
            PieceInterface::TYPE_PAWN,
            PieceInterface::TYPE_ROOK,
            PieceInterface::TYPE_KNIGHT,
            PieceInterface::TYPE_KING,
            PieceInterface::TYPE_QUEEN,
            PieceInterface::TYPE_BISHOP,
        ];

        $method = $input->getArgument('method');
        $target = $input->getOption('target');
        $force  = $input->getOption('force') ? true : false;
        if ($method && !$target) {
            throw new \InvalidArgumentException('You must provide a target when using a method');
        }

        /** @var MoveRegistry $moveRegistry */
        $moveRegistry = $this->getContainer()->get('cl_windmill.util.move_registry');
        $moveRegistry->load($method, $target);
        $moveCalculator    = new MoveCalculator();
        $boardFactory      = new BoardFactory();
        $pieceFactory      = new PieceFactory();
        $possiblePositions = BoardHelper::getAllPositions();

        foreach ([Color::WHITE, Color::BLACK] as $color) {
            foreach ($possiblePositions as $position) {
                foreach ($pieceTypes as $pieceType) {
                    if ($force !== true && $moveRegistry->has($position, $pieceType, $color)) {
                        $output->writeln(sprintf(
                            'Moves for <comment>%s</comment> with a <comment>%s</comment> from position <comment>%s</comment> have already been generated',
                            $color === Color::WHITE ? 'white' : 'black',
                            $pieceType,
                            $position
                        ));

                        continue;
                    }

                    $piece = $pieceFactory->create($pieceType, $color);
                    $board = $boardFactory->createEmpty();
                    $board->getSquare($position)->setPiece($piece);

                    $moves = $moveCalculator->possibleMovesFrom($position, $board, false)->all();

                    /** @var Move[] $moves */
                    foreach ($moves as $move) {
                        $piece = $board->getSquare($move->getFrom())->getPiece();
                        $move->setPiece($piece);
                        $moveRegistry->add($move, $force);
                    }
                }
            }
        }

        if (!$moveRegistry->isFresh()) {
            $serialized = $moveRegistry->save($method, $target);
            $output->writeln(sprintf(
                'Written <comment>%d</comment> characters to file: <comment>%s</comment>',
                strlen($serialized),
                $target
            ));
        } else {
            $output->writeln('No moves needed to be added (or the `--force` option wasn\'t used)');
        }
    }
}
