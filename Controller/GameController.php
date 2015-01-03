<?php

namespace CL\Bundle\WindmillBundle\Controller;

use CL\Windmill\Decorator\PieceDecorator;
use CL\Windmill\Model\Color;
use CL\Windmill\Model\Game\Game;
use CL\Windmill\Model\Move\Move;
use CL\Windmill\Util\StorageHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class GameController extends Controller
{
    /**
     * @return Response
     */
    public function newAction()
    {
        $game = $this->createGame();

        $this->getStorageHelper()->saveGame($game);

        return $this->redirect($this->generateUrl('cl_windmill_game_view', ['id' => $game->getId()]));
    }

    /**
     * @param string $id
     *
     * @return Response
     */
    public function viewAction($id)
    {
        $game = $this->getStorageHelper()->loadGame($id);

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

        $game = $this->getStorageHelper()->loadGame($id);
        $move = new Move($fromPosition, $toPosition);

        try {
            $game->doMove($move);
            $moved = true;
        } catch (\Exception $e) {
            $moved = false;
        }

        if ($moved) {
            $this->getStorageHelper()->saveGame($game);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->createJsonResponseFromGame($game, $moved);
        } elseif (!$moved) {
            throw $e;
        }

        return $this->redirect($this->generateUrl('cl_windmill_game_view', ['id' => $game->getId()]));
    }

    /**
     * @param Game $game
     * @param bool $ok
     *
     * @return JsonResponse
     */
    private function createJsonResponseFromGame(Game $game, $ok = false)
    {
        $moveCalculator = $this->get('cl_windmill.util.move_calculator');
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

        return new JsonResponse([
            'ok'     => $ok,
            'result' => [
                'squares' => $squares
            ],
        ]);
    }

    /**
     * @return Game
     */
    private function createGame()
    {
        $whitePlayer = $this->get('cl_windmill.util.player_factory')->create(Color::WHITE, 'Player 1', true);
        $blackPlayer = $this->get('cl_windmill.util.player_factory')->create(Color::BLACK, 'Computer', false);
        $game        = $this->get('cl_windmill.util.game_factory')->create($whitePlayer, $blackPlayer);

        return $game;
    }

    /**
     * @return StorageHelper
     */
    private function getStorageHelper()
    {
        return $this->get('cl_windmill.util.storage_helper');
    }
}
