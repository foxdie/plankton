<?php

namespace Plankton;


interface HTTPMessage{
    const CONTENT_TYPE_JSON                    = "application/json";
    const CONTENT_TYPE_XML 	                   = "application/xml";
    const CONTENT_TYPE_X_WWW_FORM_URLENCODED   = "application/x-www-form-urlencoded";
    const CONTENT_TYPE_TEXT_PLAIN              = "text/plain";
    
    public function __toString(): ?string;
}
