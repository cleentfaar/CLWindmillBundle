<?php

namespace CL\Bundle\WindmillBundle\Twig;

use CL\Windmill\Decoration\MoveDecorator;
use CL\Windmill\Decoration\PieceDecorator;
use CL\Windmill\Model\Color;
use CL\Windmill\Model\Game\GameInterface;
use CL\Windmill\Model\Square\Square;
use CL\Windmill\Util\MoveCalculator;
use CL\Windmill\Util\TemplateRegistry;
use Symfony\Component\Routing\RouterInterface;

class WindmillExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TemplateRegistry
     */
    private $templateRegistry;

    /**
     * @param \Twig_Environment $twig
     * @param RouterInterface   $router
     * @param TemplateRegistry  $templateRegistry
     */
    public function __construct(\Twig_Environment $twig, RouterInterface $router, TemplateRegistry $templateRegistry)
    {
        $this->twig             = $twig;
        $this->router           = $router;
        $this->templateRegistry = $templateRegistry;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'windmill_render_board'    => new \Twig_Function_Method($this, 'renderBoard', ['is_safe' => ['html']]),
            'windmill_render_captures' => new \Twig_Function_Method($this, 'renderCaptures', ['is_safe' => ['html']]),
            'windmill_render_clocks'   => new \Twig_Function_Method($this, 'renderClocks', ['is_safe' => ['html']]),
            'windmill_render_game'     => new \Twig_Function_Method($this, 'renderGame', ['is_safe' => ['html']]),
            'windmill_render_history'  => new \Twig_Function_Method($this, 'renderHistory', ['is_safe' => ['html']]),
            'windmill_render_vs'       => new \Twig_Function_Method($this, 'renderVs', ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param GameInterface $game
     *
     * @return string
     */
    public function renderCaptures(GameInterface $game)
    {
        $vars = [
            'white_captures'  => $game->getWhitePlayer()->getCaptures(),
            'black_captures'  => $game->getBlackPlayer()->getCaptures(),
            'piece_decorator' => new PieceDecorator(),
        ];

        $template = $this->templateRegistry->get('captures');
        $output   = $this->twig->render($template, $vars);

        return $output;
    }

    /**
     * @param GameInterface $game
     *
     * @return string
     */
    public function renderVs(GameInterface $game)
    {
        $vars = [
            'white_player' => $game->getWhitePlayer(),
            'black_player' => $game->getBlackPlayer(),
        ];

        $template = $this->templateRegistry->get('vs');
        $output   = $this->twig->render($template, $vars);

        return $output;
    }

    /**
     * @param GameInterface $game
     *
     * @return string
     */
    public function renderClocks(GameInterface $game)
    {
        $vars = [
            'white'       => [
                'move_passed'  => 5,
                'total_passed' => 95,
            ],
            'black'       => [
                'move_passed'  => 0,
                'total_passed' => 225,
            ],
            'move_limit'  => 60,
            'total_limit' => 60,
        ];

        $template = $this->templateRegistry->get('clocks');
        $output   = $this->twig->render($template, $vars);

        return $output;
    }

    /**
     * @param GameInterface $game
     *
     * @return string
     */
    public function renderGame(GameInterface $game)
    {
        $moveUrl = $this->router->generate('cl_windmill_game_move', ['id' => $game->getId()]);
        $vars    = [
            'game'             => $game,
            'move_url'         => $moveUrl,
            'windmill_options' => [
                'url' => $moveUrl,
            ],
        ];

        $template = $this->templateRegistry->get('game');
        $output   = $this->twig->render($template, $vars);

        return $output;
    }

    /**
     * @param GameInterface $game
     *
     * @return string
     */
    public function renderHistory(GameInterface $game)
    {
        $vars = [
            'white_moves'    => $game->getMovesBy(Color::WHITE),
            'black_moves'    => $game->getMovesBy(Color::BLACK),
            'move_decorator' => new MoveDecorator(new PieceDecorator()),
        ];

        $template = $this->templateRegistry->get('history');
        $output   = $this->twig->render($template, $vars);

        return $output;
    }

    /**
     * @param GameInterface $game
     * @param bool          $flipped
     *
     * @return string
     */
    public function renderBoard(GameInterface $game, $flipped = false)
    {
        $rows         = [];
        $squaresByRow = $game->getBoard()->getIndexedSquares('row');

        if ($flipped === false) {
            $squaresByRow = array_reverse($squaresByRow, true);
        }

        $pieceDecorator = new PieceDecorator();
        $moveCalculator = new MoveCalculator();

        /** @var Square[] $squares */
        foreach ($squaresByRow as $row => $squares) {
            foreach ($squares as $square) {
                $data                     = [];
                $data['possible_targets'] = [];
                $data['position']         = $square->getPosition();
                $data['color']            = $square->getColor();
                $data['color_text']       = $square->getColor() === Color::WHITE ? 'white' : 'black';
                $data['piece_type']       = null;
                $data['piece_color']      = null;
                $data['content']          = null;

                if (null !== $piece = $square->getPiece()) {
                    $data['piece_type']  = $piece->getType();
                    $data['piece_color'] = $piece->getColor();
                    $data['content']     = $pieceDecorator->toAscii($piece);
                    if ($game->getCurrentColor() === $piece->getColor()) {
                        $data['possible_targets'] = $moveCalculator->possibleMoves($square->getPosition(), $game->getBoard(), true);
                    }
                }

                $rows[$row][$square->getPosition()] = $data;
            }
        }


        $vars = [
            'game'          => $game,
            'rows'          => $rows,
            'column_labels' => ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
        ];

        $template = $this->templateRegistry->get('board');
        $output   = $this->twig->render($template, $vars);

        return $output;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'twig_extension_windmill';
    }
}
