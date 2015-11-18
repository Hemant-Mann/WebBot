<?php
namespace WebBot\lib\Exceptions;

class Core extends \Exception {
	public function __construct($msg) {
		parent::__construct($msg);
	}

	public function getCustomMessage() {
		return $this->message();
	}
}
