<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;


class TransactionForm extends AbstractForm
{
    /**
     * Constructor.
     */
    public function __construct($em = null)
    {

        // Define form name
        parent::__construct($em);
        
        $this->addElements();
//        $this->addCsrf();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {


        $this->add([
            'type' => 'text',
            'name' => 'amount',
            'options' => [
                'label' => 'Amount',
            ],
            'attributes' => [
                'class' => 'form-control underlined',
                'id' => 'amount',
                'required' => true,
                'value'=>0
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'service_tax',
            'options' => [
                'label' => 'Service Tax ('.\Application\Library\Utility::SERVICE_TAX_PER.'%)',
            ],
            'attributes' => [
                'class' => 'form-control underlined',
                'id' => 'service_tax',
                'disabled' => true,
                'value'=>0
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'vat',
            'options' => [
                'label' => '('. \Application\Library\Utility::VAT_PER.'%) of service tax as VAT',
            ],
            'attributes' => [
                'class' => 'form-control underlined',
                'id' => 'vat',
                'disabled' => true,
                'value'=>0
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
            'name' => 'amount',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter amount'
                        ]
                    ],
                ],
                [
                    'name' => 'Zend\I18n\Validator\IsFloat',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\I18n\Validator\IsFloat::INVALID => 'Please enter valid amount',
                            \Zend\I18n\Validator\IsFloat::NOT_FLOAT => 'Please enter valid amount',
                        ]
                    ],
                ],
                
            ],
        ]);
        

    }

}
