<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Application\Library\Utility;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class AbstractForm extends Form
{

    public $csrfName = "csrf";

    protected $em = null;

    protected $_excludeFields;

    protected $inputFilter;

    protected $dbAdapter;

    public function __construct($em = null, $name = null, $options = array())
    {
        $this->em = $em;
        if (empty($name)) {
            $name = $this->className();
        }
        parent::__construct($name, $options);

        // Set POST method for this form
        $this->setAttribute('method', 'post');
    }

    protected function className()
    {
        $class = explode("\\", get_class($this));
        $filter = new \Zend\Filter\Word\CamelCaseToDash();
        return strtolower($filter->filter(end($class)));
    }

    protected function addCsrf()
    {
        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => $this->csrfName,
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ]
        ]);
    }


    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function excludeFields($field = "", $value = "")
    {
        $excludeFields = null;
        if ($field != "" && $value != "") {
            $excludeFields = array(
                'field' => $field,
                'value' => $value
            );
        }
        $this->_excludeFields = $excludeFields;
    }
    
}
