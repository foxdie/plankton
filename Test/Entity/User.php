<?php

namespace Test\Entity;


class User{
	/**
	 * @access private
	 * @var int $id
	 */
	private $id;
	
	/**
	 * @access private
	 * @var string $email
	 */
	private $email;

	/**
	 * @access public
	 * @param int $id
	 */
	public function __construct(int $id){
		$this->id = $id;
		$this->email = "dummy{$id}@localhost";
	}
	
	/**
	 * @access public
	 * @return int
	 */
	public function getId(): int{
		return $this->id;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getEmail(): string{
		return $this->email;
	}

	/**
	 * @access public
	 * @param string $email
	 * @return void
	 */
	public function setEmail(string $email): void{
		$this->email = $email;
	}
}
