<?php

namespace Test\Controller;

use Rest\Server\Response;
use Rest\Server\Controller;
use Test\Entity\User;
use Rest\Server\Request;
use Rest\Exception;


class APIController extends Controller{
	/**
	 * GET example
	 * @Route(/user)
	 * @Method(GET)
	 */
	public function listUsers(Request $request): Response{
		// list users
		$page = intval($request->getParameter("page")) ?: 1;
		
		$user1 = new User(1 + 2 * ($page - 1));
		$user2 = new User(2 + 2 * ($page - 1));
		
		// response
		$response = new Response();
		$response
		->setContentType(Response::CONTENT_TYPE_JSON)
		->setCode(200)
		->setContent([
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
	public function getUser(int $id, Request $request): Response{
		// get user
		$response = new Response();
		$user = new User($id);
		
		// response
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setContent([
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
	public function createUser(Request $request): Response{
		// create user
		$id = 23; 
		$user = new User($id);
		
		// response
		$response = new Response();
		$response
			->setCode(201)
			->setLocation("/user/{$user->getId()}");
			
		return $response;
	}
	
	/**
	 * PUT example
	 * @Route(/user/{id})
	 * @Method(PUT)
	 */
	public function putUser(int $id, Request $request): Response{
		// update user
		$user = new User($id);
		$user->setEmail($request->getData("email"));
		
		// response
		$response = new Response();
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setContent([
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
	public function patchUser(int $id, Request $request): Response{
		// patch user
		$user = new User($id);
		$user->setEmail($request->getData("email"));
		
		// response
		$response = new Response();
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setContent([
				"id" 	=> $user->getId(),
				"email" => $user->getEmail()
			]);
			
		return $response;
	}
	
	/**
	 * DELETE example
	 * @Route(/user/{id})
	 * @Method(DELETE)
	 */
	public function deleteUser(int $id): Response{
		// delete user
		// ...
	
		// response
		$response = new Response();
		$response->setCode(204);
			
		return $response;
	}
	
	/**
	 * @Exception(NotFoundException)
	 * @param \Rest\Exception
	 * @param \Rest\Server\Request $request
	 * @return \Rest\Server\Response
	 */
	public function catchNotFoundException(Exception $e, Request $request): Response{
		$response = new Response();
		$response
			->setCode($e->getCode())
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setContent(["error" => $e->getMessage()]);
		
		return $response;
	}
	
	/**
	 * @Exception(*)
	 * @param \Rest\Exception
	 * @param \Rest\Server\Request $request
	 * @return \Rest\Server\Response
	 */
	public function catchException(Exception $e, Request $request): Response{
		$response = new Response();
		$response
			->setCode($e->getCode())
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setContent(["error" => $e->getMessage()]);
	
		return $response;
	}
}
