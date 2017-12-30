<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator;
use Zend\Authentication\AuthenticationService;

use Application\Entity\Twitts;
use Application\Form\TwittCreateForm;
use Application\Form\TwittEditForm;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class IndexController extends AbstractActionController
{
    /**
     * @var DoctrineORMEntityManager
     */
    protected $em;

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function removeAction()
    {
        if (!($user = $this->identity())) {
            return $this->redirect()->toRoute('user/login',
                    array(
                            'controller' => 'UsersController',
                            'action' => 'login'
                    ));
        }

        $request  = $this->getRequest();
        $twitt_id = $this->getEvent()->getRouteMatch()->getParam('id');
        $this->em = $this->getEntityManager();
        $entity   = $this->em->find('Application\Entity\Twitts',$twitt_id);
        $this->em->remove($entity);
        $this->em->flush();
        return $this->redirect()->toRoute('home');
    }

    public function createAction()
    {

        if (!($user = $this->identity())) {
            return $this->redirect()->toRoute('user/login',
                array(
                    'controller' => 'UsersController',
                    'action' => 'login'
                ));
        }

        $sm = $this->getServiceLocator();
        $this->em = $sm->get('Doctrine\ORM\EntityManager');

        $request = $this->getRequest();
        $form = new TwittCreateForm($request);

        if ($request->isPost()) {

            $form->setData($request->getPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $data['userUser'] = $user->getUserId();

                $entity = new Twitts();
                $form = new TwittCreateForm($request);
                $hydrator = new DoctrineHydrator($this->em, get_class($entity));

                $form->setHydrator($hydrator);
                $entity->setUserUser($user);
                $date = new \DateTime();
                $entity->setTwittTime($date);
                $form->bind($entity);

                $form->setData($data);
                $form->isValid();
                $this->em->persist($entity);
                $this->em->flush();
                return $this->redirect()->toRoute('home');
            }
        }

        $view = new ViewModel(array(
            'editBackofficeForm' => $form,
            'action' => 'twitter/create',
            'id' => null,
            'h1' => 'Twitt',
        ));

        $view->setTemplate('application/twitter/edit');
        return $view;
    }

    public function editAction()
    {
        if (!($user = $this->identity())) {
            return $this->redirect()->toRoute('user/login',
                array(
                    'controller' => 'UsersController',
                    'action' => 'login'
                ));
        }

        $request  = $this->getRequest();
        $twitt_id = $this->getEvent()->getRouteMatch()->getParam('id');
        $this->em = $this->getEntityManager();
        $entity   = $this->em->find('Application\Entity\Twitts',$twitt_id);

        $hydrator = new DoctrineHydrator($this->em, get_class($entity));
        $form = new TwittEditForm($request);

        $form->setHydrator($hydrator);
        $form->bind($entity);

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);

            // Nothing is validated here
            if ($form->isValid()) {

                $this->em->flush();
                return $this->redirect()->toRoute('home');
            }
        }

        $views = new ViewModel( array(
            'editBackofficeForm' => $form,
            'id' => $twitt_id,
            'action' => 'twitter/edit',
            'h1' => 'Twitt',
            'route' => 'twitter',
        ));
        $views->setTemplate('application/twitter/edit');

        return $views;
    }

    public function indexAction()
    {
        if (!($user = $this->identity())) {
            return $this->redirect()->toRoute('user/login',
                array(
                    'controller' => 'UsersController',
                    'action' => 'login'
                ));
        }

        $param_variant = null;
        $param_variant = $this->getEvent()->getRouteMatch()->getParam('variant');
        $sm = $this->getServiceLocator();
        $this->em = $sm->get('doctrine.entitymanager.orm_default');

        switch ($param_variant) {
            case 'last24hourstwitts':
                $title = 'Last 24 hours Twitts';
                $twitts = $this->em->getRepository('Application\Entity\Twitts')->findLast24hoursTwitts($user);
                break;

            case 'currusertwitts':
                $title = 'User Twitts';
                $twitts = $this->em->getRepository('Application\Entity\Twitts')->findUserTwitts($user);
                break;

            case 'followtwitts':
                $title = 'Followed Twitts';
                $twitts = $this->em->getRepository('Application\Entity\Twitts')->findFollowedTwitts($user);
                break;

            default:
                $title = 'Twitter';
                $twitts = $this->em->getRepository('Application\Entity\Twitts')->findAllTwitts($user);
                break;
        }

        if (is_array($twitts)) {
            $paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter($twitts));
        }
        else {
            $paginator =  $twitts;
        }

        $paginator->setItemCountPerPage(11);
        $param_p = $this->getEvent()->getRouteMatch()->getParam('p');
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        // translation
        $translator = $sm->get('mvcTranslator');
        $lang = $translator->getLocale();

        $views = new ViewModel(array(
            'twitts' => $paginator,
            'h1' => $title,
            'route' => 'twitter',
            'id' => 'twittId',
            'userlistElements' => $param_variant != 'currusertwitts' ?
                                    array('twittTittle', 'twittMessage', "twittTime",'userName') :
                                    array('twittTittle', 'twittMessage', "twittTime"),
        ));
        $views->setTemplate('application/twitter/index');
        return $views;
    }

    public function followAction()
    {
        $param = 'undefined';
        $param_p = $this->getEvent()->getRouteMatch()->getParam('uid');

        $request = $this->getRequest();
        if ($request->isPost()){
            $param = $request->getPost();
            $this->changeFollow($param->uid == 'follow', $param_p);
        }

        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $twitt = $em->find('Application\Entity\User', $param_p );

        $response = $this->getResponse();
        $response->setContent(\Zend\Json\Json::encode(array('param' => ($param->uid == 'follow' ? 'unfollow' : 'follow'), 'uid' => $param_p)));
        return $response;
    }

    protected function changeFollow($folow = true,$uid)
    {
        //code save to db
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        // @todo check if identity changed after flush db.
        $curruser = $this->identity();
        $userUserFollow = $em->find('Application\Entity\User', $uid );
        if ($folow) {
            $curruser->addUserUserFollow($userUserFollow);
        } else {
            $curruser->removeUserUserFollow($userUserFollow);
        }

        $em->persist($curruser);
        $em->flush();
    }
}
