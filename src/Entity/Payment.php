<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payments.
 *
 * @ORM\Table(name="payments")
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{

    /**
     *
     * @var int @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var int @ORM\ManyToOne(targetEntity="Operation", inversedBy="payments")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id")
     */
    private $operationId;

    /**
     *
     * @var int @ORM\ManyToOne(targetEntity="Account", inversedBy="payments")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $accountId;

    /**
     *
     * @var float @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     *
     * @var int @ORM\ManyToOne(targetEntity="Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currencyId;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set operationId.
     *
     * @param int $operationId
     *
     * @return Payment
     */
    public function setOperationId($operationId)
    {
        $this->operationId = $operationId;

        return $this;
    }

    /**
     * Get operationId.
     *
     * @return int
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    /**
     * Set accountId.
     *
     * @param int $accountId
     *
     * @return Payment
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId.
     *
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set amount.
     *
     * @param float $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currencyId.
     *
     * @param int $currencyId
     *
     * @return Payment
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * Get currencyId.
     *
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }
}
