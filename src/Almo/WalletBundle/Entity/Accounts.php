<?php

namespace Almo\WalletBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Accounts.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Almo\WalletBundle\Repository\AccountsRepository")
 */
class Accounts
{
    /**
     * @var int @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string @ORM\Column(name="title", type="string", length=80)
     */
    private $title;

    /**
     * @var int @ORM\ManyToOne(targetEntity="Almo\UserBundle\Entity\Users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @ORM\OneToMany(targetEntity="Payments", mappedBy="accountId")
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
     * @return Accounts
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
     * @return Accounts
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
     * @param \Almo\WalletBundle\Entity\Payments $payments
     *
     * @return Accounts
     */
    public function addPayment(\Almo\WalletBundle\Entity\Payments $payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments.
     *
     * @param \Almo\WalletBundle\Entity\Payments $payments
     */
    public function removePayment(\Almo\WalletBundle\Entity\Payments $payments)
    {
        $this->payments->removeElement($payments);
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
