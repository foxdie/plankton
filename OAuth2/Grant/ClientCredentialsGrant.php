<?php

namespace OAuth2\Grant;


class ClientCredentialsGrant implements Grant{
	const ERROR_INVALID_REQUEST 		= "invalid_request";
	const ERROR_INVALID_CLIENT 			= "invalid_client";
	const ERROR_INVALID_GRANT 			= "invalid_grant";
	const ERRORINVALID_SCOPE 			= "invalid_scope";
	const ERROR_UNAUTHORIZED_CLIENT 	= "unauthorized_client";
	const ERROR_UNSUPPORTED_GRANT_TYPE 	= "unsupported_grant_type";
}
