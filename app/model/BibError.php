<?php

class BibError {
	
	protected $requestError;
	protected $code;
	protected $message;
	protected $detail;
	function __construct() {
    }
	function setRequestError($error)
    {
        if (!is_a($error, 'GuzzleHttp\Psr7\Response')) {
            Throw new \BadMethodCallException('You must pass a valid Guzzle Http PSR7 Response');
        }
        $this->requestError = $error;
        if (implode($this->requestError->getHeader('Content-Type')) !== 'text/html;charset=utf-8'){
            $error_response = simplexml_load_string($this->requestError->getBody());
            $this->code = (integer) $error_response->code;
            $this->message = (string) $error_response->message;
            $this->detail = (string) $error_response->detail;
        } else {
            $this->code = (integer) $this->requestError->getStatusCode();
        }
    }
	
    function getRequestError()
    {
        return $this->requestError;
    }
	
	function getCode() {
        return $this->code;
    }
	
	function getMessage() {
    return $this->message;
    }

	 function getDetail() {
        return $this->detail;
    }
}