<?php

namespace Twitter\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Twitts
 *
 * @ORM\Table(name="twitts", indexes={@ORM\Index(name="fk_twitts_user1_idx", columns={"user_user_id"}), @ORM\Index(name="time_athor", columns={"twitt_time", "user_user_id"}), @ORM\Index(name="titile", columns={"twitt_tittle"})})
 * @ORM\Entity
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
     * @var \Twitter\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Twitter\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_user_id", referencedColumnName="user_id")
     * })
     */
    private $userUser;


}
