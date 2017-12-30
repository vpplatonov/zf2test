<?php
namespace Application\Controller;

use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Form\SearchForm;
use Application\Entity\Twitts;

class SearchController extends AbstractActionController
{
    public function indexAction()
    {
        if (!($user = $this->identity())) {
            return $this->redirect()->toRoute('user/login',
                    array(
                            'controller' => 'UsersController',
                            'action' => 'login'
                    ));
        }

        $request = $this->getRequest();
        $searchResults = [];

        if ($request->isPost()) {

            $queryText = $request->getPost('query');

            $searchIndexLocation = $this->getIndexLocation();
            $index = Lucene::open($searchIndexLocation);

            $searchResults = $index->find($queryText);
        }

        $form = new SearchForm();
        $viewModel = new ViewModel(array(
                'form' => $form,
                'searchResults' => $searchResults,
        ));
        $viewModel->setTemplate('application/search/index');
        return $viewModel;
    }

    protected function getIndexLocation()
    {
        $config = $this->getServiceLocator()->get('config');
        if ($config instanceof traversable) {
            $config = ArrayUtils::iterarorToArray($config);
        }

        if (!empty($config['module_config']['search_index'])) {
            return $config['module_config']['search_index'];
        }
        return false;
    }

    public function generateIndexAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute('user/login',
                    array(
                            'controller' => 'UsersController',
                            'action' => 'login'
                    ));
        }

        $searchIndexLocation = $this->getIndexLocation();

        if (!$searchIndexLocation) {
            throw new Exception('Haven\'t Index Location for Search index');
        }

   //     \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8());
        $index = Lucene::create($searchIndexLocation);

        $sm = $this->getServiceLocator();
        $em = $sm->get('doctrine.entitymanager.orm_default');
        $twitts = $em->getRepository('Application\Entity\Twitts')->findAll();

        foreach($twitts as $key => $twitt) {

            $twitt_author = Document\Field::unIndexed('twitt_author',$twitt->getUserId());
            $twitt_id = Document\Field::unIndexed('twitt_id',$twitt->getTwittId());
            $twitt_title = Document\Field::Keyword('twitt_title',$twitt->getTwittTittle());
            $twitt_message = Document\Field::Text('twitt_message',$twitt->getTwittMessage());
            $twitt_time = Document\Field::unIndexed('twitt_time', $twitt->getTwittTime());

            $doc =new Document();
            $doc->addField($twitt_title);
            $doc->addField($twitt_message);
            $doc->addField($twitt_id);
            $doc->addField($twitt_author);

            $doc->addField($twitt_time);
            $index->addDocument($doc);
        }

        $index->commit();
    }
}
