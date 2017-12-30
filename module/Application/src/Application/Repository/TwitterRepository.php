<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;

use Application\Entity\User;
use Application\Entity\Twitts;

/**
 * Extends Twitter Entity with find methods
 *
 * @author platonov
 *
 */
class TwitterRepository extends EntityRepository
{
    /**
     * Native SQL
     * @var Doctrine\ORM\Query\ResultSetMapping
     */
    private $rsm;

    /**
     * Initial function for Doctrine Native Query SQL
     *
     * @return \Application\Repository\Doctrine\ORM\Query\ResultSetMapping
     */
    private function initRsm()
    {
        $this->rsm = new ResultSetMapping();
        $this->rsm->addEntityResult('Application\Entity\Twitts', 'twts');
        $this->rsm->addFieldResult('twts', 'twitt_tittle', 'twittTittle');
        $this->rsm->addFieldResult('twts', 'twitt_message', 'twittMessage');
        $this->rsm->addFieldResult('twts', 'twitt_time', 'twittTime');
        $this->rsm->addFieldResult('twts', 'twitt_id', 'twittId');
        $this->rsm->addScalarResult('is_follow', 'isFollow');

        $this->rsm->addJoinedEntityResult('Application\Entity\User' , 'u', 'twts', 'userUser');
        $this->rsm->addFieldResult('u', 'display_name', 'displayName');
        $this->rsm->addFieldResult('u', 'user_id', 'userId');

        return $this->rsm;
    }

    /**
     * for REST service
     *
     * @return rendered string or JSON
     */
    public function findAllTwittsForRest()
    {
        /**
         * Use DQL
         * @var SQL query string
         */
        $rsq = $this->_em->createQuery("
                 SELECT
                    twts.twittTittle,
                    twts.twittMessage,
                    twts.twittTime,
                    twts.twittId,
                    u.displayName,
                    u.userId
                 FROM Application\Entity\Twitts twts
                 JOIN twts.userUser u
                ORDER BY twts.twittTime DESC");

        return $rsq->getResult();
    }

    /**
     * Select Last 24 hours Twitts published on site
     * with calculated value if current user followed author of twitt.
     *
     * @param User $user
     * @return \Application\Entity\Twitts Collection
     */
    public function findLast24hoursTwitts(User $user)
    {
        $this->initRsm();
        $rsq = $this->_em->createNativeQuery("
                SELECT
                    twts.twitt_tittle,
                    twts.twitt_message,
                    twts.twitt_time,
                    twts.twitt_id,
                    u.display_name,
                    u.user_id,
                	IFNULL( (SELECT twitter_follow_user.user_user_id
                               FROM twitter_follow_user
                    		  WHERE user_user_follow_id = twts.user_user_id
                    			AND user_user_id = ? ),-1) as is_follow
                 FROM twitts as twts
                 LEFT JOIN user as u ON u.user_id = twts.user_user_id
                WHERE twts.twitt_time > NOW() - INTERVAL 1 DAY
                ORDER BY twts.twitt_time DESC", $this->rsm);

        $rsq->setParameter(1, $user->getUserId());
        return $rsq->getResult();
    }

    /**
     * Find all twitts published on site
     *
     * @param User $user
     * @return \Application\Entity\Twitts Collection
     */
    public function findAllTwitts(User $user)
    {
        $this->initRsm();
        $rsq = $this->_em->createNativeQuery("
                SELECT
                    twts.twitt_tittle,
                    twts.twitt_message,
                    twts.twitt_time,
                    twts.twitt_id,
                    u.display_name,
                    u.user_id,
                	IFNULL( (SELECT twitter_follow_user.user_user_id
                               FROM twitter_follow_user
                    		  WHERE user_user_follow_id = twts.user_user_id
                    			AND user_user_id = ? ),-1) as is_follow
                 FROM twitts as twts
                 LEFT JOIN user as u ON u.user_id = twts.user_user_id
                ORDER BY twts.twitt_time DESC", $this->rsm);

        $rsq->setParameter(1, $user->getUserId());
        return $rsq->getResult();
    }

    /**
     * Find current Logged in user twitts.
     *
     * @param User $user
     * @return \Application\Entity\Twitts Collection
     */
    public function findUserTwitts(User $user)
    {
        $this->initRsm();
        $rsq = $this->_em->createNativeQuery("
                SELECT
                    twts.twitt_tittle,
                    twts.twitt_message,
                    twts.twitt_time,
                    twts.twitt_id,
                    u.display_name,
                    u.user_id
                FROM twitts as twts
                LEFT JOIN user as u ON u.user_id = twts.user_user_id
                WHERE twts.user_user_id = ?
                ORDER BY twts.twitt_time DESC",$this->rsm);

        $rsq->setParameter(1, $user->getUserId());
        return $rsq->getResult();
    }

    /**
     * Find All twitts of users followed current Logged in user.
     *
     * @param User $user
     * @return \Application\Entity\Twitts Collection
     */
    public function findFollowedTwitts(User $user)
    {
        $this->initRsm();
        $rsq = $this->_em->createNativeQuery("
                SELECT
                    twts.twitt_tittle,
                    twts.twitt_message,
                    twts.twitt_time,
                    u.display_name,
                    u.user_id,
                	tfu.user_user_id as is_follow
                FROM twitts as twts
                LEFT JOIN user as u ON u.user_id = twts.user_user_id
                LEFT JOIN twitter_follow_user as tfu ON tfu.user_user_follow_id = u.user_id
                WHERE tfu.user_user_id = ?
                ORDER BY twts.twitt_time DESC",$this->rsm);

        $rsq->setParameter(1, $user->getUserId());
        return $rsq->getResult();
    }
}
