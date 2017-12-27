<?php

namespace Application\Service;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class CustomerManager
{

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * This method adds a new user.
     */
    public function add($data)
    {

        $obj = new \Application\Entity\Customer();
        $obj->exchangeArray($data);
        $this->entityManager->persist($obj);
        $this->entityManager->flush();

        return $obj;
    }

    /**
     * This method updates data of an existing user.
     */
    public function update($user, $data)
    {

        $user->setName($data['name']);
        $user->setDescription($data['description']);

        // Apply changes to database.
        $this->entityManager->flush();

        return true;
    }

    public function addTransaction($customer, $data)
    {

        $obj = new \Application\Entity\Transaction();
        $obj->exchangeArray($data);
        $obj->setCustomer($customer);
        $this->entityManager->persist($obj);
        $this->entityManager->flush();

        return $obj;
    }

    public function updateTransaction($transaction, $data)
    {

        $transaction->exchangeArray($data);
        $this->entityManager->flush();

        return $transaction;
    }

}
