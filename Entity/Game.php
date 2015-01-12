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
     * @var bool $finished
     *
     * @ORM\Column(type="boolean")
     */
    protected $finished = false;

    /**
     * @var int $finishedReason
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $finishedReason;

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
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setFinishedReason($reason)
    {
        $this->finishedReason = $reason;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFinishedReason()
    {
        return $this->finishedReason;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFinished()
    {
        return $this->finished === true;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setWhitePlayerName($whitePlayerName)
    {
        $this->whitePlayerName = $whitePlayerName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWhitePlayerName()
    {
        return $this->whitePlayerName;
    }


    /**
     * {@inheritdoc}
     */
    public function isWhitePlayerHuman()
    {
        return $this->whitePlayerHuman;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setWhitePlayerHuman($whitePlayerHuman)
    {
        $this->whitePlayerHuman = $whitePlayerHuman;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setBlackPlayerName($blackPlayerName)
    {
        $this->blackPlayerName = $blackPlayerName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlackPlayerName()
    {
        return $this->blackPlayerName;
    }


    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setBlackPlayerHuman($blackPlayerHuman)
    {
        $this->blackPlayerHuman = $blackPlayerHuman;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isBlackPlayerHuman()
    {
        return $this->blackPlayerHuman;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function setDatetimeCreated($datetimeCreated)
    {
        $this->datetimeCreated = $datetimeCreated;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatetimeCreated()
    {
        return $this->datetimeCreated;
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    public function addState(PersistableGameStateInterface $state)
    {
        $state->setGame($this);

        $this->states->add($state);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastState()
    {
        return $this->getStates()->last() ?: null;
    }
}
