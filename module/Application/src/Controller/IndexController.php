<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Customer;

/**
 * This is the main controller class of the User Demo application. It contains
 * site-wide actions such as Home or About.
 */
class IndexController extends AbstractActionController
{

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $customerManager;

    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager, \Application\Service\CustomerManager $customerManager)
    {
        $this->entityManager = $entityManager;
        $this->customerManager = $customerManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the 
     * Home page.
     */
    public function indexAction()
    {
        $customer = $this->entityManager->getRepository(Customer::class)
                ->findBy([], ['id' => 'ASC']);

        return new ViewModel([
            'data' => $customer
        ]);
    }

    public function addCustomerAction()
    {
        $form = new \User\Form\CustomerForm($this->entityManager);
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if ($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                $this->customerManager->add($data);

                // Redirect to "view" page
                return $this->redirect()->toRoute('home');
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editCustomerAction()
    {
        $id = (int) $this->params()->fromRoute('id', -1);

        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $customer = $this->entityManager->getRepository(Customer::class)
                ->find($id);

        if ($customer == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $serializer = new \Application\Library\EntitySerializer($this->entityManager);

        // Create user form
        $form = new \User\Form\CustomerForm($this->entityManager, $customer);
        $form->setData($serializer->toArray($customer));
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if ($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Update the user.
                $this->customerManager->update($customer, $data);

                // Redirect to "view" page
                return $this->redirect()->toRoute('home');
            }
        }
        $model = new ViewModel(array(
            'form' => $form
        ));
        $model->setTemplate('application\index\add-customer');
        return $model;
    }

    public function transactionAction()
    {
        $return = $this->__validateTransaction();
        $transactions = $this->entityManager->getRepository(\Application\Entity\Transaction::class)
                ->findBy(['customer'=>$return->getId()], ['id' => 'DESC']);

        return new ViewModel([
            'data' => $transactions,
            'customer' => $return
        ]);
    }

    public function addTransactionAction()
    {

        $customer = $this->__validateTransaction();
        $form = new \User\Form\TransactionForm();
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if ($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                $this->customerManager->addTransaction($customer, $data);

                // Redirect to "view" page
                return $this->redirect()->toRoute('application',['action'=>'transaction', 'id'=>$customer->getId()]);
            }
        }

        return new ViewModel([
            'form' => $form,
            'customer' => $customer
        ]);
    }

    
    public function editTransactionAction()
    {

        $customer = $this->__validateTransaction();
        
        $transactionId = (int) $this->params()->fromRoute('param', -1);

        if ($transactionId < 1) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $transaction = $this->entityManager->getRepository(\Application\Entity\Transaction::class)
                ->find($transactionId);

        if ($transactionId == null) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }
        
        $form = new \User\Form\TransactionForm();
         $serializer = new \Application\Library\EntitySerializer($this->entityManager);

        // Create user form
        $form->setData($serializer->toArray($transaction));
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if ($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                $this->customerManager->updateTransaction($transaction, $data);

                // Redirect to "view" page
                return $this->redirect()->toRoute('application',['action'=>'transaction', 'id'=>$customer->getId()]);
            }
        }

        $model =  new ViewModel([
            'form' => $form,
            'customer' => $customer
        ]);
       
        $model->setTemplate('application\index\add-transaction');
        return $model;
    }

    
    private function __validateTransaction()
    {
        $id = (int) $this->params()->fromRoute('id', -1);

        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $customer = $this->entityManager->getRepository(Customer::class)
                ->find($id);

        if ($customer == null) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }
        return $customer;
    }

}
