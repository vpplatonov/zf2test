<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Doctrine\ORM\EntityManager;

use Application\Entity\Twitts;

class TwitterRestfullController extends AbstractRestfulController
{
    public function get($id)
    {
        $response = $this->getResponseWithHeader()
        ->setContent( __METHOD__.' get current data with id =  '.$id);
        return $response;
    }

    public function getList()
    {
        $sm = $this->getServiceLocator();
        $em = $sm->get('Doctrine\ORM\EntityManager');
        $twitts = $em->getRepository('Application\Entity\Twitts')->findAllTwittsForRest();

        $response = $this->getResponseWithHeader()
        ->setContent(json_encode(array('data' => $twitts)));
        // need to have using getMock Doctrine\ORM\EntityManager in Unit Test as in
        // http://www.codesynthesis.co.uk/tutorials/mocking-doctrine-2-entities-in-phpunit-with-zend-2
        // ->setContent( __METHOD__.' get the list of data');

        return $response;
    }

    public function create($data)
    {
        $this->forward()->dispatch('Application\Controller\Index', array(
                'action'   => 'create',
                'twittdata' => $data,
        ));
        $response = $this->getResponseWithHeader()
        ->setContent( __METHOD__.' create new item of data :
                                                    <b>'.$data['twitt_tittle'].'</b>');
        return $response;
    }

    public function update($id, $data)
    {
        $response = $this->getResponseWithHeader()
        ->setContent(__METHOD__.' update current data with id =  '.$id);
        return $response;
    }

    public function delete($id)
    {
        $response = $this->getResponseWithHeader()
        ->setContent(__METHOD__.' delete current data with id =  '.$id) ;
        return $response;
    }

    public function getResponseWithHeader()
    {
        $response = $this->getResponse();
        $response->getHeaders()
        ->addHeaderLine('Access-Control-Allow-Origin','*')
        ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET');

        return $response;
    }

}
