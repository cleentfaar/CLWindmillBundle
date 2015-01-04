<?php

namespace CL\Bundle\WindmillBundle\Entity;

use CL\Windmill\Model\Game\PersistableGameInterface;
use CL\Windmill\Model\Game\PersistableGameStateInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class Game implements PersistableGameInterface
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
     * @ORM\Column(type="string", unique=true)
     */
    protected $uid;

    /**
     * @var int $checkmate
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $checkmate;

    /**
     * @var bool $finished
     *
     * @ORM\Column(type="boolean")
     */
    protected $finished = false;

    /**
     * @var string $whitePlayerName
     *
     * @ORM\Column(type="string")
     */
    protected $whitePlayerName;

    /**
     * @var bool $whitePlayerHuman
     *
     * @ORM\Column(type="boolean")
     */
    protected $whitePlayerHuman = false;

    /**
     * @var string $blackPlayerName
     *
     * @ORM\Column(type="string")
     */
    protected $blackPlayerName;

    /**
     * @var bool $blackPlayerHuman
     *
     * @ORM\Column(type="boolean")
     */
    protected $blackPlayerHuman = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_created", type="datetime")
     */
    protected $datetimeCreated;

    /**
     * @var GameState[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GameState", mappedBy="game", cascade={"persist"})
     */
    protected $states;

    public function __construct()
    {
        $this->states          = new ArrayCollection();
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
     * @param string $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param int $checkmate
     */
    public function setCheckmate($checkmate)
    {
        $this->checkmate = $checkmate;
    }

    /**
     * @return int
     */
    public function getCheckmate()
    {
        return $this->checkmate;
    }

    /**
     * @param bool $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * @return bool
     */
    public function hasFinished()
    {
        return $this->finished === true;
    }

    /**
     * Set whitePlayerName
     *
     * @param string $whitePlayerName
     *
     * @return Game
     */
    public function setWhitePlayerName($whitePlayerName)
    {
        $this->whitePlayerName = $whitePlayerName;

        return $this;
    }

    /**
     * Get whitePlayerName
     *
     * @return string
     */
    public function getWhitePlayerName()
    {
        return $this->whitePlayerName;
    }


    /**
     * @return boolean
     */
    public function isWhitePlayerHuman()
    {
        return $this->whitePlayerHuman;
    }

    /**
     * Set whitePlayerHuman
     *
     * @param string $whitePlayerHuman
     *
     * @return Game
     */
    public function setWhitePlayerHuman($whitePlayerHuman)
    {
        $this->whitePlayerHuman = $whitePlayerHuman;

        return $this;
    }

    /**
     * Set blackPlayerName
     *
     * @param string $blackPlayerName
     *
     * @return Game
     */
    public function setBlackPlayerName($blackPlayerName)
    {
        $this->blackPlayerName = $blackPlayerName;

        return $this;
    }

    /**
     * Get blackPlayerName
     *
     * @return string
     */
    public function getBlackPlayerName()
    {
        return $this->blackPlayerName;
    }


    /**
     * Set blackPlayerHuman
     *
     * @param string $blackPlayerHuman
     *
     * @return Game
     */
    public function setBlackPlayerHuman($blackPlayerHuman)
    {
        $this->blackPlayerHuman = $blackPlayerHuman;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBlackPlayerHuman()
    {
        return $this->blackPlayerHuman;
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
     * @param PersistableGameStateInterface $state
     *
     * @return Game
     */
    public function addState(PersistableGameStateInterface $state)
    {
        $state->setGame($this);

        $this->states->add($state);

        return $this;
    }

    /**
     * Get states
     *
     * @return GameState[]|ArrayCollection
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * @return GameState|null
     */
    public function getLastState()
    {
        return $this->getStates()->last() ?: null;
    }
}
