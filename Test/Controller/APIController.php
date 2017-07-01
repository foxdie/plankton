<?php

namespace Test\Controller;

use Rest\Response;
use Rest\Server\Controller;
use Test\Entity\User;
use Rest\Request;


class APIController extends Controller{
	/**
	 * GET example
	 * @Route(/user)
	 * @Method(GET)
	 */
	public function listUsers(){
		$response = new Response();
		
		$user1 = new User(1);
		$user2 = new User(2);
		
		$response
		->setContentType(Response::CONTENT_TYPE_JSON)
		->setCode(200)
		->setData([
			[ "id" 	=> $user1->getId(), "email" => $user1->getEmail()],
			[ "id" 	=> $user2->getId(), "email" => $user2->getEmail()]
		]);
			
		return $response;
	}
	
	/**
	 * GET example
	 * @Route(/user/{id})
	 * @Method(GET)
	 */
	public function getUser($id){
		$response = new Response();
		$user = new User($id);
		
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setData([
				"id" 	=> $user->getId(), 
				"email" => $user->getEmail()
			]);
			
		return $response;
	}

	/**
	 * POST example
	 * @Route(/user)
	 * @Method(POST)
	 */
	public function createUser($id){
		$response = new Response();
	
		$id = 1; //create user
		$user = new User($id);
		
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(201)
			->setLocation("/admin/user/{$user->getId()}");
			
		return $response;
	}
	
	/**
	 * PUT example
	 * @Route(/user/{id})
	 * @Method(PUT)
	 */
	public function updateUser($id){
		$response = new Response();
	
		//update
		$user = new User($id);
		$user->setEmail($_POST["email"]);
		
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setData([
				"id" 	=> $user->getId(),
				"email" => $user->getEmail()
			]);
			
		return $response;
	}
	
	/**
	 * PATCH example
	 * @Route(/user/{id})
	 * @Method(PATCH)
	 */
	public function updateUserEmail($id){
		$response = new Response();

		//fake post data for the demo
		$_POST["email"] = "dummy@localhost";
		
		//patch
		$user = new User($id);
		$user->setEmail($_POST["email"]);
		
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setData([
				"id" 	=> $user->getId(),
				"email" => $user->getEmail()
			]);
			
		return $response;
	}
	
	/**
	 * PATCH example
	 * @Route(/user/{id})
	 * @Method(DELETE)
	 */
	public function deleteUser($id){
		$response = new Response();
	
		//delete user
		//...
	
		$response->setCode(204);
			
		return $response;
	}
}
