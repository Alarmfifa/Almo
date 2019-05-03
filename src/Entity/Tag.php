<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag.
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
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
     * @var int @ORM\Column(name="pay_type", type="integer")
     */
    private $payType;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="operations")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

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
     * @return Tag
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
     * Set payType.
     *
     * @param int $payType
     *
     * @return Tag
     */
    public function setPayType($payType)
    {
        $this->payType = $payType;

        return $this;
    }

    /**
     * Get payType.
     *
     * @return int
     */
    public function getPayType()
    {
        return $this->payType;
    }

    /**
     * magic for textual representation.
     */
    public function __toString()
    {
        return self::getTitle();
    }

    /**
     * Set userId.
     *
     * @param \App\Entity\User $userId
     *
     * @return Tag
     */
    public function setUserId(\App\Entity\User $userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return \App\Entity\User
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
