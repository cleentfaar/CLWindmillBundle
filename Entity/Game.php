<?php

namespace CL\Bundle\WindmillBundle\Entity;

use CL\Windmill\Representation\Game\GameInterface;
use CL\Windmill\Storage\PotentialGameInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="game", indexes={@ORM\Index(columns={"state_id"})})
 * @ORM\Entity
 */
class Game implements PotentialGameInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var array
     *
     * @ORM\Column(name="white_player", type="json_array", nullable=false)
     */
    protected $whitePlayer;

    /**
     * @var array
     *
     * @ORM\Column(name="black_player", type="json_array", nullable=false)
     */
    protected $blackPlayer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_created", type="datetime")
     */
    protected $datetimeCreated;

    /**
     * @var GameState
     *
     * @ORM\ManyToOne(targetEntity="GameState", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     * })
     */
    protected $state;

    public function __construct()
    {
        $this->datetimeCreated = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set whitePlayer
     *
     * @param array $whitePlayer
     *
     * @return Game
     */
    public function setWhitePlayer(array $whitePlayer)
    {
        $this->whitePlayer = $whitePlayer;

        return $this;
    }

    /**
     * Get whitePlayer
     *
     * @return array
     */
    public function getWhitePlayer()
    {
        return $this->whitePlayer;
    }

    /**
     * Set blackPlayer
     *
     * @param array $blackPlayer
     *
     * @return Game
     */
    public function setBlackPlayer(array $blackPlayer)
    {
        $this->blackPlayer = $blackPlayer;

        return $this;
    }

    /**
     * Get blackPlayer
     *
     * @return array
     */
    public function getBlackPlayer()
    {
        return $this->blackPlayer;
    }

    /**
     * Set datetimeCreated
     *
     * @param \DateTime $datetimeCreated
     *
     * @return Game
     */
    public function setDatetimeCreated($datetimeCreated)
    {
        $this->datetimeCreated = $datetimeCreated;

        return $this;
    }

    /**
     * Get datetimeCreated
     *
     * @return \DateTime
     */
    public function getDatetimeCreated()
    {
        return $this->datetimeCreated;
    }

    /**
     * Set state
     *
     * @param GameState $state
     *
     * @return Game
     */
    public function setState(GameState $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return GameState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function extract()
    {
        return [
            'white_player' => $this->getWhitePlayer(),
            'black_player' => $this->getBlackPlayer(),
            'board'        => $this->getState()->getBoard(),
            'storage'      => 'orm',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function inject(GameInterface $game)
    {
        if ($this->getWhitePlayer() === null) {
            $this->setWhitePlayer($game->getWhitePlayer()->toArray());
        }
        if ($this->getBlackPlayer() === null) {
            $this->setBlackPlayer($game->getBlackPlayer()->toArray());
        }
        if ($this->id === null && $game->getId() !== null) {
            $this->id = $game->getId();
        }
        $state = new GameState();
        $state->setBoard($game->getBoard()->toArray());
        $state->setCurrentColor($game->getCurrentColor());
        $this->setState($state);
    }
}
