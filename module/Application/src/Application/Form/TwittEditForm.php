<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Zend\Form\Element;

use Application\Entity\Twitts;
use Application\Form\TwittCreateForm;

class TwittEditForm extends TwittCreateForm
{
/*
    public function __construct($request)
    {
        parent::__construct($request);

        $this->add(array(
         'name' => 'userUser',
         'type' => 'Hidden',
        ));
    }
*/
    public function populateFromTwitt(Twitts $twitt)
    {
        foreach ($this->getElements() as $element) {
            /** @var $element \Zend\Form\Element */
            $elementName = $element->getName();
            if (strpos($elementName, 'password') === 0) continue;

            $getter = $this->getAccessorName($elementName, false);

            if (method_exists($twitt, $getter)) {
                $element->setValue(call_user_func(array($twitt, $getter)));
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
