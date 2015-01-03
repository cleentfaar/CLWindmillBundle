<?php

namespace CL\Bundle\WindmillBundle\Entity;

use CL\Windmill\Storage\Adapter\Orm\PersistableGameInterface;
use CL\Windmill\Storage\Adapter\Orm\PersistableGameStateInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class GameState implements PersistableGameStateInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $color;
    
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $fromPosition;
    
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $toPosition;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $moveType;

    /**
     * @var array|null
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $piece;

    /**
     * @var array|null
     *
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $capture;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $squares = [];

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $datetimeCreated;

    /**
     * @var PersistableGameInterface $game
     *
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="states", cascade={"persist"})
     */
    protected $game;

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
     * Set color
     *
     * @param int|null $color
     *
     * @return GameState
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return int|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * {@inheritdoc}
     */
    public function setFrom($from)
    {
        $this->fromPosition = $from;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom()
    {
        return $this->fromPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function setTo($to)
    {
        $this->toPosition = $to;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo()
    {
        return $this->toPosition;
    }

    /**
     * @param int|null $moveType
     */
    public function setMoveType($moveType)
    {
        $this->moveType = $moveType;
    }

    /**
     * @return int|null
     */
    public function getMoveType()
    {
        return $this->moveType;
    }

    /**
     * {@inheritdoc}
     */
    public function setPiece(array $piece = null)
    {
        $this->piece = $piece;
    }

    /**
     * {@inheritdoc}
     */
    public function getPiece()
    {
        return $this->piece;
    }


    /**
     * {@inheritdoc}
     */
    public function setSquares(array $squares)
    {
        $this->squares = $squares;
    }

    /**
     * {@inheritdoc}
     */
    public function getSquares()
    {
        return $this->squares;
    }

    /**
     * {@inheritdoc}
     */
    public function setCapture(array $capture = null)
    {
        $this->capture = $capture;
    }

    /**
     * {@inheritdoc}
     */
    public function getCapture()
    {
        return $this->capture;
    }

    /**
     * {@inheritdoc}
     */
    public function setGame(PersistableGameInterface $game)
    {
        $this->game = $game;
    }

    /**
     * {@inheritdoc}
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param \DateTime $datetimeCreated
     */
    public function setDatetimeCreated(\DateTime $datetimeCreated)
    {
        $this->datetimeCreated = $datetimeCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeCreated()
    {
        return $this->datetimeCreated;
    }
}
