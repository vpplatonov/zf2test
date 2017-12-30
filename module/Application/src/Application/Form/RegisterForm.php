<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Application\Entity\User;

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
                'name' => 'user_id',
                'type' => 'Hidden',
        ));

        $this->add(array(
           'name' => 'username',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                 'label' => 'Full Name',
            ),
        ));

        $this->add(array(
            'name' => 'displayName',
            'attributes' => array(
                'type' => 'text',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Nick Name',
            ),
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
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                        'value' => 'Go',
                        'id' => 'submitbutton',
                ),
        ));
    }

    public function populateFromUser(User $user)
    {
        foreach ($this->getElements() as $element) {
            /** @var $element \Zend\Form\Element */
            $elementName = $element->getName();
            if (strpos($elementName, 'password') === 0) continue;

            $getter = $this->getAccessorName($elementName, false);

            if (method_exists($user, $getter)) {
                $element->setValue(call_user_func(array($user, $getter)));
            }
        }
    }

    protected function getAccessorName($property, $set = true)
    {
        $parts = explode('_', $property);
        array_walk($parts, function (&$val) {
            $val = ucfirst($val);
        });
            return (($set ? 'set' : 'get') . implode('', $parts));
    }
}
