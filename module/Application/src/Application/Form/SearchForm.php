<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Form\Element;

use Application\Entity\Twitts;

class SearchForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Search');

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
                'name' => 'query',
                'attributes' => array(
                        'type' => 'text',
                        'id' =>'queryText',
                        'required' => 'required',
                ),
                'options' => array(
                        'label' => 'Search Twitt',
                ),
        ));
        $this->add(array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                        'value' => 'Search',
                        'id' => 'submitbutton',
                ),
        ));

    }
}
