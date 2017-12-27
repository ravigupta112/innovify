<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;


class CustomerForm extends AbstractForm
{

    private $_customer = null;
    
    /**
     * Constructor.
     */
    public function __construct($em = null, $customer =null)
    {

        // Define form name
        parent::__construct($em);
        $this->_customer = $customer;

        $this->csrfName = "company_signup_token";
        $this->addElements();
//        $this->addCsrf();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {


        // Add "email" field
        $this->add([
            'type' => 'text',
            'name' => 'name',
            'options' => [
                'label' => 'Name',
            ],
            'attributes' => [
                'class' => 'form-control underlined',
                'id' => 'name',
                'required' => true,
            ],
        ]);

        $this->add([
            'type' => 'textarea',
            'name' => 'description',
            'options' => [
                'label' => 'Description',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control underlined',
                'id' => 'description',
                'Placeholder' => 'Describe the customer',
            ],
        ]);



        // Add the Submit button
        $this->add([
            'type' => 'submit',
            'name' => 'create',
            'attributes' => [
                'value' => 'Save',
                'id' => 'create',
                'class' => 'btn btn-block btn-primary',
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        // Add input for "email" field
        $inputFilter->add([
            'name' => 'name',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter enter name'
                        ]
                    ],
                ],
                [
                    'name' => \User\Validator\CustomerExistsValidator::class,
                    'options' => [
                        'entityManager' => $this->em,
                        'customer'=> $this->_customer
                    ],
                ],
            ],
        ]);


        $inputFilter->add([
            'name' => 'description',
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
            ],
        ]);
    }

}
