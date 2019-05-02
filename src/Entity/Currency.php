<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Currency.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Currency
{
    /**
     * @var int @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string @ORM\Column(name="title", type="string", length=20)
     */
    private $title;

    /**
     * @var string @ORM\Column(name="short", type="string", length=5)
     */
    private $short;

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
     * @return Currency
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
     * Set short.
     *
     * @param string $short
     *
     * @return Currency
     */
    public function setShort($short)
    {
        $this->short = $short;

        return $this;
    }

    /**
     * Get short.
     *
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * magic for textual representation.
     */
    public function __toString()
    {
        return self::getShort();
    }
}
