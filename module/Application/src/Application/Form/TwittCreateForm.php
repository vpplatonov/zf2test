<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Zend\Form\Element;

class TwittCreateForm extends Form
{
    protected $request;

    public function __construct($request)
    {
        parent::__construct('Twitt');
        $this->request = $request;

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
            'name' => 'twittId',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'twittTittle',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Twitt Title',
            ),
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
            'name' => 'twittMessage',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Twitt Message',
            ),
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
                'type' => 'Csrf',
                'name' => 'csrf',
        ));

        $this->add(array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                        'value' => 'Save',
                        'id' => 'submitbutton',
                ),
            ),
            array(
                 'priority' => -100, // Increase value to move to top of form
            )
        );
    }
}
