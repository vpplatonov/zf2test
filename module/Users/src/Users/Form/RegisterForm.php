<?php

namespace Users\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RegisterForm extends Form
{
    public $type;
    public function __construct($name = null)
    {
        parent::__construct('Register');
        $this->type = $name ? $name : "Register";
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
                'name' => 'uid',
                'type' => 'Hidden',
        ));

        $this->add(array(
           'name' => 'name',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                 'label' => 'Full Name',
            ),
        ));
        $this->add(array(
            'name' => 'mail',
            'attributes' => array(
                 'type' => 'email',
                 'required' => 'required',
            ),
            'options' => array(
                 'label' => 'Email',
            ),
            'filters' => array(
               array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                  'name' => 'EmailAddres',
                   'options' => array(
                       'domain' => true,
                    )
                )
            ),
        ));
        $this->add(array(
                'name' => 'pass',
                'attributes' => array(
                        'type' => 'password',
                        'required' => 'required',
                ),
                'options' => array(
                        'label' => 'Password',
                ),
                'filters' => array(
                        array('name' => 'StringTrim'),
                ),
        ));
        $this->add(array(
                'name' => 'confirm_password',
                'attributes' => array(
                        'type' => 'password',
                        'required' => 'required',
                ),
                'options' => array(
                        'label' => 'Confirm Password',
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
                        'value' => 'Go',
                        'id' => 'submitbutton',
                ),
            ),
            array(
                    'priority' => -100, // Increase value to move to top of form
            )
        );
    }
}
