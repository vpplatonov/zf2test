<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Form\Element;

use Application\Entity\User;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Login');

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
                'name' => 'userId',
                'type' => 'Hidden',
        ));

        $this->add(array(
                'name' => 'email',
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
                'name' => 'password',
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
                'type' => 'Csrf',
                'name' => 'csrf',
        ));

        $this->add(array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                        'value' => 'Login',
                        'id' => 'submitbutton',
                ),
                array(
                        'priority' => -100, // Increase value to move to top of form
                )
        ));
    }
}
