<?php
namespace User\Validator;

use Zend\Validator\AbstractValidator;
use Application\Entity\Customer;

/**
 * This validator class is designed for checking if there is an existing customer
 * with such an email.
 */
class CustomerExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'customer' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR  = 'notScalar';
    const CUSTOMER_EXISTS = 'customerExists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR  => "The name must be a scalar value",
        self::CUSTOMER_EXISTS  => "Name already exists"
    );

    /**
     * Constructor.
     */
    public function __construct($options = null)
    {
        // Set filter options (if provided).
        if(is_array($options)) {
            if(isset($options['entityManager']))
                $this->options['entityManager'] = $options['entityManager'];
            if(isset($options['customer']))
                $this->options['customer'] = $options['customer'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if customer exists.
     */
    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        // Get Doctrine entity manager.
        $entityManager = $this->options['entityManager'];

        $customer = $entityManager->getRepository(Customer::class)
                ->findOneByName($value);

        if($this->options['customer']==null) {
            $isValid = ($customer==null);
        } else {
            if($this->options['customer']->getName()!=$value && $customer!=null)
                $isValid = false;
            else
                $isValid = true;
        }

        // If there were an error, set error message.
        if(!$isValid) {
            $this->error(self::CUSTOMER_EXISTS);
        }

        // Return validation result.
        return $isValid;
    }
}

