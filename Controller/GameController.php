<?php

namespace CL\Bundle\WindmillBundle\Controller;

use CL\Windmill\Model\Color;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Stopwatch\Stopwatch;

class GameController extends Controller
{
    public function indexAction()
    {
        $stopwatchEvents = [];
        $stopWatch       = new Stopwatch();
        $stopWatch->start('create_game');
        $options                        = [
            'white_player' => [
                'color' => Color::WHITE,
                'name'  => 'Player 1',
            ],
            'black_player' => [
                'color'            => Color::BLACK,
                'name'             => 'Player 2',
                'human_controlled' => false,
            ],
            'storage'      => 'orm',
        ];
        $game                           = $this->get('cl_windmill.model.game_factory')->create($options);
        $stopwatchEvents['create_game'] = $stopWatch->stop('create_game');

        return $this->render('CLWindmillBundle:Game:index.html.twig', array('game' => $game, 'stopwatch_events' => $stopwatchEvents));
    }
}
