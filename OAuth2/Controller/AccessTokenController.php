<?php

namespace OAuth2\Controller;

use Rest\Request;
use Rest\Server\Response;
use Rest\Server\Controller;
use Rest\Exception;


class AccessTokenController extends Controller{
	/**
	 * @Route(/token)
	 * @Method(GET)
	 */
	public function createAccessToken(Request $request): Response{	
		$response = new Response();
		
		$data = [
			"access_token" 	=> "",
			"token_type" 	=> "",
			"expires_in" 	=> "",
			"refresh_token" => "",
			"scope"			=> ""
		];
		
		$response->setCode(200)
			->setContent($data)
			->setHeader("Cache-Control", "no-store")
			->setHeader("Pragma", "no-cache");		
		
		return $response;
	}

	/**
	 * @Exception(OAuth2Exception)
	 */
	public function catchException(OAuth2Exception $e, Request $request): Response{
		$response = new Response();
		$response
			->setCode($e->getCode())
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setContent(["error" => $e->getMessage()]);
		
		return $response;
	}
}

