<?php

namespace Almo\WalletBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payments
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Almo\WalletBundle\Repository\PaymentsRepository")
 */
class Payments
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * 
     * @ORM\ManyToOne(targetEntity="Operations", inversedBy="payments")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id")
     */
    private $operationId;


    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Accounts", inversedBy="payments")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $accountId;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var integer
     * 
     * @ORM\ManyToOne(targetEntity="Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currencyId;


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
     * Set operationId
     *
     * @param integer $operationId
     * @return Payments
     */
    public function setOperationId($operationId)
    {
        $this->operationId = $operationId;

        return $this;
    }

    /**
     * Get operationId
     *
     * @return integer 
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     * @return Payments
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return integer 
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Payments
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
     * Set currencyId
     *
     * @param integer $currencyId
     * @return Payments
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * Get currencyId
     *
     * @return integer 
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }
}