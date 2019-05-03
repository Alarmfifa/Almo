<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 * @ORM\Table(name="operations")
 */
class Operation
{

    /**
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="operations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     *
     * @ORM\Column(type="string", length=80)
     */
    private $title;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $notice;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     */
    private $tagId;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     *
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     *
     * @ORM\OneToMany(targetEntity="Payment", mappedBy="operationId", cascade={"persist"})
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
     * Set user_id.
     *
     * @param int $userId
     *
     * @return Operation
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get user_id.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Operation
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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Operation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set notice.
     *
     * @param string $notice
     *
     * @return Operation
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice.
     *
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Set tagId.
     *
     * @param \App\Entity\Tag $tagId
     *
     * @return Operation
     */
    public function setTagId(\App\Entity\Tag $tagId = null)
    {
        $this->tagId = $tagId;

        return $this;
    }

    /**
     * Get tagId.
     *
     * @return \App\Entity\Tag
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Operation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * @return Operation
     */
    public function addPayment(\App\Entity\Payment $payment)
    {
        // don't forget to add reference id
        $payment->setOperationId($this);
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
