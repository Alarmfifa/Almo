<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Accounts.
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
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
     * @var string @ORM\Column(name="title", type="string", length=80)
     */
    private $title;

    /**
     *
     * @var int @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     *
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="accountId")
     */
    private $payments;

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
     * Set title.
     *
     * @param string $title
     *
     * @return Account
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return Account
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * magic for textual representation.
     */
    public function __toString()
    {
        return self::getTitle();
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add payments.
     *
     * @param \App\Entity\Payment $payments
     *
     * @return Account
     */
    public function addPayment(\App\Entity\Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove payments.
     *
     * @param \App\Entity\Payment $payments
     */
    public function removePayment(\App\Entity\Payment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }
}
