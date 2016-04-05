<?php
namespace WebBot\Core;
use WebBot\Exceptions\Document as Doc;

class Document {
    
    /**
     * Store Document Response object
     * @var \WebBot\HTTP\Response object
     */
    protected $_response_obj;
    
    /**
     * Store Document ID
     * @var int|string
     */
    public $id;
    
    /**
     * Store Document URL
     * @var string
     */
    public $uri;
    
    /**
     * Stores the xPath of the Document for web scrapping
     * @var \DOMXPath object
     */
    protected $_xPath = null;
    
    public function __construct($response, $id, $uri) {
        $this->_response_obj = $response;
        $this->id = $id;
        $this->uri = $uri;
    }
    
    /**
     * getter
     * @return \WebBot\HTTP\Response object
     */
    public function getHttpResponse() {
        return $this->_response_obj;
    }

    public function getBody() {
        return $this->_response_obj->getBody();
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
        if (preg_match('#^\/.*\/$#', $value)) {
            return preg_match($value . 'Usm' . ($case_insensitive ? 'i' : ''), $this->getBody());
        } else {	// no regex, use string position
            return call_user_func(($case_insensitive ? 'stripos' : 'strpos'), $this->getBody(), $value);
        }
        
        return false;
    }
    
    /**
     * Find a given pattern using preg_match or str_pos
     *
     * @return array|string|boolean
     */
    public function find($value, $read_length_or_str = 0, $case_insensitive = true) {
        if ($this->test($value, $case_insensitive)) {
            if (preg_match('#^\/.*\/$#', $value)) {
                preg_match_all($value, $this->getBody(), $m);
                return $m;
            } else {
                 // no regex, use string position
                $pos = call_user_func(($case_insensitive ? 'stripos' : 'strpos'), $this->getBody(), $value);
                
                if (is_string($read_length_or_str)) {
                    $pos+= strlen(value);
                     // move position length of value
                    $pos_end = call_user_func(($case_insensitive ? 'stripos' : 'strpos'), $this->getBody(), $read_length_or_str);
                    
                    echo "start: $pos, end: $pos_end<br />";
                    if ($pos_end !== false && $pos_end > $pos) {
                        $diff = $pos_end - $pos;
                        return substr($this->getBody(), $pos, $diff);
                    }
                } else {
                    $read_length = (int)$read_length_or_str; // int read length
                    
                    return $read_length < 1 ? substr($this->getBody(), $pos) : substr($this->getBody(), $pos, $read_length);
                }
            }
        } else {
            return false;
        }
    }
    
    /**
     * Checks to see if the xPath of the Document has already been set and then
     * returns the xPath object
     */
    public function returnXPathObject() {
        if (!$this->_xPath) {
            $xmlPageDom = new \DomDocument(); // Instantiating a new DomDocument object
            @$xmlPageDom->loadHTML($this->getBody()); // Loading the HTML from downloaded page
            $this->_xPath = new \DOMXPath($xmlPageDom); // Instantiating new XPath DOM object
        }
        return $this->_xPath;
    }
    
    /**
     * Queries the Document's xPath object to find the given element throws an
     * exception if unable to find the element in DOM
     *
     * @param string $xPath The XPath query string
     * @return \DOMXPath object
     */
    public function query($q, $contextNode = null) {
		if (!$this->_xPath) {
			$this->returnXPathObject();
		}

        if ($contextNode) {
            $el = $this->_xPath->query($q, $contextNode);
        } else {
            $el = $this->_xPath->query($q);
        }
        $length = $el->length;
        
        if ($length === 0) {
            throw new Doc("Element doesn't exist in DOM");
        }

        return $el;
    }
}
