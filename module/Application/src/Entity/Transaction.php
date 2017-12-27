<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction", indexes={@ORM\Index(name="customer_id", columns={"customer_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Transaction extends AbstractEntity
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", precision=10, scale=0, nullable=false)
     */
    protected $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="service_tax", type="float", precision=10, scale=0, nullable=true)
     */
    protected $serviceTax;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", precision=10, scale=0, nullable=true)
     */
    protected $vat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var \Customer
     *
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * })
     */
    protected $customer;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set serviceTax
     *
     * @param float $serviceTax
     *
     * @return Transaction
     */
    public function setServiceTax($serviceTax)
    {
        $this->serviceTax = $serviceTax;

        return $this;
    }

    /**
     * Get serviceTax
     *
     * @return float
     */
    public function getServiceTax()
    {
        return $this->serviceTax;
    }

    /**
     * Set vat
     *
     * @param float $vat
     *
     * @return Transaction
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat
     *
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Transaction
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Transaction
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set customer
     *
     * @param \Customer $customer
     *
     * @return Transaction
     */
    public function setCustomer(Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @ORM\PrePersist
     */
    public function doStuffOnPostPersist()
    {
        $this->createdAt = new \DateTime(\Application\Library\Utility::curDateTime());
        $this->calculateTax();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new  \DateTime(\Application\Library\Utility::curDateTime());
        $this->calculateTax();
    }

    protected function calculateTax(){
        $amtArr = \Application\Library\Utility::getFinalAmount($this->amount, true);
        $this->serviceTax = isset($amtArr['service_tax']) ? $amtArr['service_tax'] : 0;
        $this->vat = isset($amtArr['vat']) ? $amtArr['vat'] : 0;
    }
}
