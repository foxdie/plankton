<?php

namespace Test\Entity;


class User{
	private $id;
	private $email;

	public function __construct($id){
		$this->id = $id;
		$this->email = "dummy{$id}@localhost";
	}
	
	public function getId(){
		return $this->id;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}
}
