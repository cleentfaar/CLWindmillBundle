<?php

namespace CL\Bundle\WindmillBundle\Controller;

use CL\Windmill\Decorator\PieceDecorator;
use CL\Windmill\Model\Color;
use CL\Windmill\Model\Game\Game;
use CL\Windmill\Model\Game\GameInterface;
use CL\Windmill\Model\Move\Move;
use CL\Windmill\Util\LazyMoveCalculator;
use CL\Windmill\Util\StorageHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class GameController extends Controller
{
    /**
     * @return Response
     */
    public function newAction()
    {
        $game = $this->createGame();

        if (!$this->getStorageHelper()->saveGame($game)) {
            throw new ServiceUnavailableHttpException('The game could not be stored by the configured adapter');
        }

        return $this->redirect($this->generateUrl('cl_windmill_game_view', ['id' => $game->getId()]));
    }

    /**
     * @param string $id
     *
     * @return Response
     */
    public function viewAction($id)
    {
        $game  = $this->loadGame($id);

        return $this->render('CLWindmillBundle:Game:index.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse|RedirectResponse
     */
    public function moveAction(Request $request, $id)
    {
        $fromPosition = $request->get('from');
        $toPosition   = $request->get('to');

        if (!$fromPosition || !$toPosition) {
            throw new NotAcceptableHttpException('Must supply both a `from` and a `to`-position');
        }

        $game  = $this->loadGame($id);
        $error = $this->processMove($game, $fromPosition, $toPosition);

        if ($request->isXmlHttpRequest()) {
            return $this->createJsonResponseFromGame($game, $error);
        } elseif ($error !== null) {
            throw new NotAcceptableHttpException($error);
        }

        return $this->redirect($this->generateUrl('cl_windmill_game_view', ['id' => $game->getId()]));
    }

    /**
     * @param GameInterface $game
     * @param int           $from
     * @param int           $to
     *
     * @return string|null
     */
    private function processMove(GameInterface $game, $from, $to)
    {
        try {
            $error = null;

            $game->doMove($from, $to);
            $game->finishTurn($this->getMoveCalculator());

            $this->getStorageHelper()->saveGame($game);

            if (!$game->hasFinished() && !$game->getCurrentPlayer()->isHuman()) {
                $game->doEngineMove($this->get('cl_windmill.util.move_calculator'));
                $game->finishTurn($this->getMoveCalculator());

                $this->getStorageHelper()->saveGame($game);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return $error;
    }

    /**
     * @param GameInterface $game
     * @param string|null   $error
     *
     * @return JsonResponse
     */
    private function createJsonResponseFromGame(GameInterface $game, $error = null)
    {
        $moveCalculator = $this->getMoveCalculator();
        $pieceDecorator = new PieceDecorator();
        $squares        = [];
        foreach ($game->getBoard()->getSquares() as $square) {
            $squareData = [
                'color'            => $square->getColor(),
                'color_text'       => $square->getColor() === Color::WHITE ? 'white' : 'black',
                'position'         => $square->getPosition(),
                'content'          => null,
                'possible_targets' => [],
            ];

            if (null !== $piece = $square->getPiece()) {
                $squareData['piece_type']  = $piece->getType();
                $squareData['piece_color'] = $piece->getColor();
                $squareData['content']     = $pieceDecorator->toAscii($piece);
                if ($game->getCurrentColor() === $piece->getColor()) {
                    $squareData['possible_targets'] = $moveCalculator->possibleMovesFrom(
                        $square->getPosition(),
                        $game->getBoard(),
                        true
                    );
                }
            }

            $squares[] = $squareData;
        }

        $responseData = [
            'ok'     => $error === null,
            'result' => [
                'squares' => $squares
            ],
        ];

        if ($error !== null) {
            $responseData['error'] = $error;
        }

        return new JsonResponse($responseData);
    }

    /**
     * @param $id
     *
     * @return GameInterface
     */
    private function loadGame($id)
    {
        $game = $this->getStorageHelper()->loadGame($id);
        if ($game === null) {
            throw $this->createNotFoundException(sprintf('There is no game with this ID: %s', $id));
        }

        return $game;
    }

    /**
     * @return Game
     */
    private function createGame()
    {
        $whitePlayer = $this->get('cl_windmill.util.player_factory')->createWhite('Player 1', true);
        $blackPlayer = $this->get('cl_windmill.util.player_factory')->createBlack('Player 2', true);
        //$blackPlayer = $this->get('cl_windmill.util.player_factory')->create(Color::BLACK, 'Computer', false);
        $game        = $this->get('cl_windmill.util.game_factory')->create($whitePlayer, $blackPlayer);

        return $game;
    }

    /**
     * @return LazyMoveCalculator
     */
    private function getMoveCalculator()
    {
        return $this->get('cl_windmill.util.move_calculator');
    }

    /**
     * @return StorageHelper
     */
    private function getStorageHelper()
    {
        return $this->get('cl_windmill.util.storage_helper');
    }
}
