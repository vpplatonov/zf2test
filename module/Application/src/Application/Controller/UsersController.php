<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator;

use Application\Entity\User;
//use Users\Model\UsersTable;
use Application\Form\RegisterForm;
use Application\Form\LoginForm;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class UsersController extends AbstractActionController
{
    protected $usersTable;
    public $action_message = 'Register Sucessfull';
    public $confirm_message = 'Thank you for your registration';
    protected $authservice;

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

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $this->em = $sm->get('doctrine.entitymanager.orm_default');
        if (!($user = $this->identity())) {
            return $this->redirect()->toRoute('user/login',
                    array(
                            'controller' => 'UsersController',
                            'action' => 'login'
                    ));
        }
        $users = $this->em->getRepository('Application\Entity\User')->listUserFollowed($user);

        if (is_array($users)) {
            $paginator = new Paginator\Paginator(new Paginator\Adapter\ArrayAdapter($users));
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

        $view = new ViewModel(
               array(
                   'twitts' => $paginator,
                   'h1' => "I followed",
                   'route' => 'user',
                   'id' => 'userId',
                   'userlistElements' => array('display_name', 'email'),
                ));
        $view->setTemplate('application/twitter/users/index');
        return $view;
    }

    public function editAction()
    {
    //    $user_id = $this->getEvent()->getRouteMatch()->getParam('id');
        $this->em = $this->getEntityManager();
        if (!($user = $this->identity())) {
            return $this->redirect()->toRoute('user/login',
                array(
                    'controller' => 'UsersController',
                    'action' => 'login'
                ));
        }
        $entity = $user; //$this->em->getRepository('Application\Entity\User')->find($user->getUserId());

        $hydrator = new DoctrineHydrator($this->em, get_class($entity));
        $form = new RegisterForm();
        $form->remove('confirm_password');
     //   $form->populateFromUser($user);
        $form->setHydrator($hydrator);
        $form->bind($entity);

        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                $this->em->flush();
                return $this->redirect()->toRoute('user/edit',
                    array(
                        'controller' => 'UsersController',
                        'action' => 'edit'
                    ));
            }
        }

        $views = new ViewModel( array(
                'editBackofficeForm' => $form,
                'id' => $user->getUserId(),
                'action' => 'user/edit',
                'h1' => 'User',
                'route' => 'user',
        ));
        $views->setTemplate('application/twitter/edit');

        return $views;
    }

    public function registerAction()
    {
        $form = new RegisterForm();
        $view = new ViewModel(array('form' => $form));
        $view->setTemplate('application/users/register/register');
        return $view;
    }

    public function postloginAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('user/login',
                array(
                    'controller' => 'UsersController',
                    'action' => 'login'
                ));
        }
        // If you used another name for the authentication service, change it here
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');

        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($this->request->getPost('email'));
        $adapter->setCredentialValue($this->request->getPost('password'));
        $result = $authService->authenticate();

        if ($result->isValid()) {
            $identity = $result->getIdentity();
            $authService->getStorage()->write($identity);

            $this->action_message = 'Login Sucessfull';
     //       $user_mail = $authService->getStorage()->read();
            $this->confirm_message = 'Welcome! ' . $this->request->getPost('email');
            return $this->redirect()->toRoute('twitter' , array(
                 'controller' => 'UsersController',
                 'action' => 'index',
            ));

        } else {
            $post = $this->request->getPost();
            $form = new LoginForm();
            $form->setData($post);
            $model = new viewModel(array(
                 'error' => true,
                 'form' => $form,
                 'message' => '',
            ));
            $model->setTemplate('application/users/users/login');
            return $model;
        }
    }

    public function processAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('user/register',
                    array(
                      'controller' => 'UsersController',
                      'action' => 'register'
                    ));
        }
        $post = $this->request->getPost();
        $form = new RegisterForm();
        // $inputFilter = new RegisterFilter();
        // $form->setInputFilter($inputFilter);
        $form->setData($post);
        if(!$form->isValid()) {
            $model = new viewModel(array(
                    'error' => true,
                    'form' => $form,
            ));
            $model->setTemplate('application/users/register/register');
            return $model;
        }
        $this->createUser($form->getData());
        $this->action_message = 'Register Sucessfull';
        $this->confirm_message = 'Thank you for your registration';
        return $this->redirect()->toRoute('user/confirm',array(
                'controller' => 'UsersController',
                'action' => 'confirm'
        ));
    }

    protected function createUser(array $data) {

        $sm = $this->getServiceLocator();
        $this->em = $sm->get('Doctrine\ORM\EntityManager');

        $entity = new User();
        $hydrator = new DoctrineHydrator($this->em, get_class($entity));

        $form = new RegisterForm();
        $form->remove('confirm_password');
        $form->setHydrator($hydrator);

        $form->bind($entity);

        $request = $this->getRequest();
        if ($request->isPost()) {
        //    $data = $request->getPost();
            $form->setData($data);

            // Nothing is validated here
            if ($form->isValid()) {

                $this->em->persist($entity);
                $this->em->flush();
                return $this->redirect()->toRoute('home');
            }
        }

        return true;
    }

    public function confirmAction()
    {
        $view = new ViewModel(array(
              'action' =>  $this->action_message,
              'message' => $this->confirm_message,
        ));
        $view->setTemplate('application/users/register/confirm');
        return $view;
    }

    public function loginAction()
    {
        $form = new LoginForm();
        $view = new ViewModel(array('form' => $form));
        $view->setTemplate('application/users/users/login');
        return $view;
    }

    public function logoutAction()
    {
      //  $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('user/login',
                    array(
                      'controller' => 'UsersController',
                      'action' => 'login'
                    ));
    }

    public function getAuthService()
    {
        if (! $this->authservice) {
            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
            $this->authservice = $authService;
        }
        return $this->authservice;
    }
}

