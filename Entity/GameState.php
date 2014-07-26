<?php

namespace CL\Bundle\WindmillBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameState
 *
 * @ORM\Table(name="game_states")
 * @ORM\Entity
 */
class GameState
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
     * @var string
     *
     * @ORM\Column(name="current_color", type="string", length=255, nullable=false)
     */
    protected $currentColor;

    /**
     * @var array
     *
     * @ORM\Column(name="board", type="json_array", nullable=false)
     */
    protected $board;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_created", type="datetime")
     */
    protected $datetimeCreated;

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
     * Set currentColor
     *
     * @param int $currentColor
     *
     * @return GameState
     */
    public function setCurrentColor($currentColor)
    {
        $this->currentColor = $currentColor;

        return $this;
    }

    /**
     * Get currentColor
     *
     * @return int
     */
    public function getCurrentColor()
    {
        return $this->currentColor;
    }

    /**
     * Set board
     *
     * @param array $board
     *
     * @return GameState
     */
    public function setBoard(array $board)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board
     *
     * @return array
     */
    public function getBoard()
    {
        return $this->board;
    }
}
