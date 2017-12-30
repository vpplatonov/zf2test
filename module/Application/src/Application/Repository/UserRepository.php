<?php
namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;

use Application\Entity\User;
use Application\Entity\Twitts;

/**
 *
 * @author platonov
 *
 */
class UserRepository extends EntityRepository
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
        // use Doctrine\ORM\Query\ResultSetMapping;
        $this->rsm = new ResultSetMapping();
        $this->rsm->addEntityResult('Application\Entity\User', 'u');
        $this->rsm->addFieldResult('u', 'display_name', 'displayName');
        $this->rsm->addFieldResult('u', 'email', 'email');
        $this->rsm->addFieldResult('u', 'user_id', 'userId');
        // How much twitts user made/
        $this->rsm->addScalarResult('num_twitts', 'numTwitts');
        return $this->rsm;
    }

    /**
     * Select Users followed by current logged in user.
     *
     * @return array of User
     * @param User $user
     */
    public function listUserFollowed(User $user)
    {
        $this->initRsm();
        $rsq = $this->_em->createNativeQuery("
                SELECT
                     u.display_name,
                     u.email,
                     u.user_id,
                     uf.user_follow_user_time,
                    (SELECT COUNT(twitt_id)
                       FROM twitts as twts WHERE user_user_id = uf.user_user_follow_id ) as num_twitts
                  FROM twitter_follow_user as uf
                  LEFT JOIN user as u ON u.user_id = uf.user_user_follow_id
                 WHERE uf.user_user_id = ? ", $this->rsm);

        $rsq->setParameter(1, $user->getUserId());
        return $rsq->getResult();
    }
}
