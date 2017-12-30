<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Twitts
 *
 * @ORM\Table(name="twitts", indexes={@ORM\Index(name="fk_twitts_user1_idx", columns={"user_user_id"}), @ORM\Index(name="time_athor", columns={"twitt_time", "user_user_id"}), @ORM\Index(name="titile", columns={"twitt_tittle"})})
 * @ORM\Entity(repositoryClass="Application\Repository\TwitterRepository")
 */
class Twitts
{
    /**
     * @var integer
     *
     * @ORM\Column(name="twitt_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $twittId;

    /**
     * @var string
     *
     * @ORM\Column(name="twitt_message", type="string", length=255, nullable=true)
     */
    private $twittMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="twitt_tittle", type="string", length=45, nullable=false)
     */
    private $twittTittle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="twitt_time", type="datetime", nullable=false)
     */
    private $twittTime;

    /**
     * @var \Application\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_user_id", referencedColumnName="user_id")
     * })
     */
    private $userUser;

    /**
     * Get twittId
     *
     * @return integer
     */
    public function getTwittId()
    {
        return $this->twittId;
    }

    /**
     * Set twittMessage
     *
     * @param string $twittMessage
     * @return Twitts
     */
    public function setTwittMessage($twittMessage)
    {
        $this->twittMessage = $twittMessage;

        return $this;
    }

    /**
     * Get twittMessage
     *
     * @return string
     */
    public function getTwittMessage()
    {
        return $this->twittMessage;
    }

    /**
     * Set twittTittle
     *
     * @param string $twittTittle
     * @return Twitts
     */
    public function setTwittTittle($twittTittle)
    {
        $this->twittTittle = $twittTittle;

        return $this;
    }

    /**
     * Get twittTittle
     *
     * @return string
     */
    public function getTwittTittle()
    {
        return $this->twittTittle;
    }

    /**
     * Set twittTime
     *
     * @param \DateTime $twittTime
     * @return Twitts
     */
    public function setTwittTime($twittTime)
    {
        $this->twittTime = $twittTime;

        return $this;
    }

    /**
     * Get twittTime
     *
     * @return \DateTime
     */
    public function getTwittTime()
    {
        if ($this->twittTime)
            return $this->twittTime->format('Y-m-d H:s');
        else
            return $this->twittTime;
    }

    /**
     * Set userUser
     *
     * @param \Application\Entity\User $userUser
     * @return Twitts
     */
    public function setUserUser(\Application\Entity\User $userUser = null)
    {
        $this->userUser = $userUser;

        return $this;
    }

    /**
     * Get userUser
     *
     * @return \Application\Entity\User
     */
    public function getUserUser()
    {
        return $this->userUser;
    }

    /**
     * Get userUserId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userUser->getUserId();
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userUser->getDisplayName();
    }
}
