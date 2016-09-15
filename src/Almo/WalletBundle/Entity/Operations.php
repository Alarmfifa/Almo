<?php

namespace Almo\WalletBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Almo\WalletBundle\Entity\OperationsRepository")
 * @ORM\Table()
 * 
 */
class Operations
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Almo\UserBundle\Entity\Users", inversedBy="operations")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * 
	 */
	private $userId;
	
	/**
	 * @ORM\Column(type="string", length=80)
	 */
	private $title;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $notice;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Tags")
	 * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
	 */
	private $tagId;

	
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $date;
	
	/**
	 * @ORM\Column(type="string", length=20)
	 */
	private $type;
	
	/**
	 * @ORM\OneToMany(targetEntity="Payments", mappedBy="operationId", cascade={"persist"})
	 */
	private $payments;


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
     * Set user_id
     *
     * @param integer $userId
     * @return Operations
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Operations
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Operations
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set notice
     *
     * @param string $notice
     * @return Operations
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice
     *
     * @return string 
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Set tagId
     *
     * @param \Almo\WalletBundle\Entity\Tags $tagId
     * @return Operations
     */
    public function setTagId(\Almo\WalletBundle\Entity\Tags $tagId = null)
    {
        $this->tagId = $tagId;

        return $this;
    }

    /**
     * Get tagId
     *
     * @return \Almo\WalletBundle\Entity\Tags 
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Operations
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add payments
     *
     * @param \Almo\WalletBundle\Entity\Payments $payments
     * @return Operations
     */
    public function addPayment(\Almo\WalletBundle\Entity\Payments $payments)
    {
    	// don't forget to add reference id
        $payments->setOperationId($this);
        $this->payments[] = $payments;
        
        return $this;
    }

    /**
     * Remove payments
     *
     * @param \Almo\WalletBundle\Entity\Payments $payments
     */
    public function removePayment(\Almo\WalletBundle\Entity\Payments $payments)
    {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments()
    {
        return $this->payments;
    }
}
