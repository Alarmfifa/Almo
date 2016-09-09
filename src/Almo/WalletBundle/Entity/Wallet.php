<?php
namespace Almo\WalletBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class Wallet
{
	/**
	 * @Assert\NotBlank(message = "Enter date please")
	 * @Assert\Type("\DateTime")
	 */
	protected $date;
	
	/**
	 * @Assert\Length(min=10)
	 * @Assert\NotBlank()
	 */
	protected $title;
	
	public function getDate()
	{
		return $this->date;
	}
	
	public function setDate(\DateTime $date = null)
	{
		$this->date = $date;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	
	
}

?>