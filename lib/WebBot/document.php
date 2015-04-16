<?php
namespace WebBot;

class Document {

	/**
    * Store Document Response object
    * @var \HTTP\Response object
    */
	private $response_obj;
	
	/**
    * Store Document ID
    * @var int
    */
	public $id;

	/**
    * Store Document URL
    * @var string
    */
	public $url;

	/**
	* Successful fetch flag
	*
	* @var boolean
	*/
	public $success;

	/**
    * Error message (false when no errors)
    *
    * @var boolean|string
    */
	public $error;

	public function __construct(\HTTP\Response $response, $id) {

		$this->response_obj = $response;
		$this->id = $id;

		if($this->response_obj->getStatusCode() != 200) {
			$this->error = "Error: ". $this->response_obj->getStatusCode();
			$this->success = false;
		} else {
			$this->success = true;
		}
	}

	/**
    * getter
    * @return \HTTP\Response object
    */
	public function getHttpResponse() {
		return $this->response_obj;
	}

	/**
	* Test if value/pattern exists in reponse data
	*
	* @param mixed $value (value or regex pattern, if regex pattern donot
	* pattern modifiers and use regex delims '/', ex: '/pattern/')
	* @param boolean $case_insensitive
	* @return boolean
	*/
	public function test($value, $case_insensitive = true) {
		if(preg_match('#^\/.*\/$#', $value)) { // regex pattern
			return preg_match($value. 'Usm'. ($case_insensitive ? 'i': ''), $this->getHttpResponse()->getBody());
		} else { // no regex, use string position
			return call_user_func(($case_insensitive ? 'stripos' : 'strpos'), $this->getHttpResponse()->getBody(), $value);
		}

		return false;
	}

	/**
	* Find a given pattern using preg_match or str_pos
	*
	* @return array|string|boolean
	*/
	public function find($value, $read_length_or_str = 0, $case_insensitive = true) {
		if($this->test($value, $case_insensitive)) {
			if(preg_match('#^\/.*\/$#', $value)) { // regex pattern
				preg_match_all($value, $this->getHttpResponse()->getBody(), $m);

				return $m;
			} else { // no regex, use string position
				$pos = call_user_func(($case_insensitive ? 'stripos' : 'strpos'), $this->getHttpResponse()->getBody(), $value);

				if(is_string($read_length_or_str)) {
					$pos += strlen(value); // move position length of value
					$pos_end = call_user_func(($case_insensitive ? 'stripos' : 'strpos' ), $this->getHttpResponse()->getBody(), $read_length_or_str);

					echo "start: $pos, end: $pos_end<br />";
					if($pos_end !== false && $pos_end > $pos) {
						$diff = $pos_end - $pos;
						return substr($this->getHttpResponse()->getBody(), $pos, $diff);
					}
				} else { // int read length
					$read_length = (int) $read_length_or_str;

					return $read_length < 1 ? substr($this->getHttpResponse()->getBody(), $pos) : substr($this->getHttpResponse()->getBody(), $pos, $read_length);
				}
			}
		} else {
			return false;
		} 
	}

	/**
	* @param string $pattern, $subject
	* @return array
	*/
	private function match($pattern, $subject) {
		preg_match_all($pattern, $subject, $matched);
		if(!$matched) {	return [];	}
		return $matched;
	}

	/**
	* Accepts an array of URLs
	* Filter relative and absolute URL's in a new array
	* Execute WebBot request for the URL contained in array
	*
	* @param array $links (URL)
	* @return array \WebBot\Document object
	*/
	public function spider($links) {
		// Check if the links list is empty
		if(empty($links)) {
			return false;
		}

		$list = '';
		foreach ($links as $url) {
			$list .= " '{$url}' ";
		}

		// The link attribute can be either relative or absolute

		$rel_path = $this->match("/['][\/]?([^\/][^http][\w+].*?)[']/", $list);
		// separates out relative link Eg: /login stores each link in $rel_path[1] array

		$abs_path = $this->match("/['](http(s)?:.*?)[']/i", $list);
		// separates out absolute link Eg: http://www.facebook.com/ stores each link in $abs_path[1] array

		$path = $this->match("/[']([\/][\/].*?)[']/", $list);
		// separates out link starting with //www.domain.com, stores each link $path[1] array

		$list = []; $i = 0;
		// Append domain name to relative paths
		foreach ($rel_path[1] as $address) {
			$list["{$i}"] = $this->url.$address;
			$i++;
		}

		foreach ($abs_path[1] as $address) {
			$list["{$i}"] = $address;
			$i++;
		}

		// Find the protocol of the domain because the url //www.something.com
		// will execute over the same protocol which is being currently used
		// If it is http then url => http://www...   if https then url => https:
		preg_match('/(https?:)/i', $this->url, $protocol);

		foreach ($path[1] as $address) {
			$list["{$i}"] = $protocol[1].$address;
			$i++;
		}

		$spider = new \WebBot\WebBot($list);
		$spider->execute();
		return $spider->getDocuments();
	}
}

?>