<?php
/**
 * Created by PhpStorm.
 * User: xinzheng
 * Date: 21/08/2014
 * Time: 11:29 PM
 */



class ShareRails {
    /**instance variables*/

    //time stamp
    private $timestamp = "";

    //post data
    private $postData = "";

    //post data hash
    private $postDataHash = "";

    //protocol, http or https, etc. Default protocol is http
    private $protocol = "http";

    //domain
    private $domain = "";

    //url path
    private $path = "";

    //storename
    private $storeName = "";

    //secret string
    private $secret = "";

    //query
    private $query = array();

    /** Constructor */
    public function __construct($currentStoreName,$curentSecret){
        $this->storeName = $currentStoreName;
        $this->secret = $curentSecret;
    }

    /** getter and setter */
    //get timestamp
    public function getTimestamp(){
        return $this->timestamp;
    }

    //set timestamp
    public function setTimestamp($currentTimeStamp){
        $this->timestamp = $currentTimeStamp;
    }

    //get postdata
    public function getPostData(){
        return $this->postData;
    }

    //set postdata
    public function setPostData($currentPostData){
        $this->postData = $currentPostData;
    }

    //get postData hash
    public function getPostDataHash(){
        return md5($this->postData);
    }

    //get protocol
    public function getProtocol(){
        return $this->protocol;
    }

    //set protocol
    public function setProtocol($currentProtocol){
        $this->protocol = $currentProtocol;
    }

    //get domain
    public function getDomain(){
        return $this->domain;
    }

    //set domain
    public function setDomain($currentDomain){
        $this->domain = $currentDomain;
    }

    //get path
    public function getPath(){
        return $this->path;
    }

    //set path
    public function setPath($currentPath){
        $this->path = $currentPath;
    }

    //get store name
    public function getStoreName(){
        return $this->storeName;
    }

    //set store name
    public function setStoreName($currentStoreName){
        $this->storeName = $currentStoreName;
    }

    //get secret
    public function getSecret(){
        return $this->secret;
    }

    //set secret
    public function setSecret($currentSecret){
        $this->secret = $currentSecret;
    }

    //get query
    public function getQuery(){
        return $this->query;
    }

    //set query
    public function setQuery($currentQuery){
        $this->query = $currentQuery;
    }

    /**Methods */
    public function post(){

        $currentQuery = $this->getQuery();

        $queryString = '?'.http_build_query($currentQuery);

        $sec = $this->secret;

        // Data used for hmac
        $hamcData = $this->protocol .'://'.$this->getDomain().$this->getPath().$queryString;

        // Generate Signature
        $signature = base64_encode(hash_hmac("sha1", $hamcData, $sec));


        // request querystring (note this does not contain the md5 data)

        $requestQueryString = '?'.http_build_query(array(
                'app' => $this->storeName,
                'timestamp' => $this->getTimestamp(),
                'signature' => $signature
        ));

        //create request url
        $url = $this->getProtocol() . '://' . $this->getDomain().$this->getPath().$requestQueryString;

        //options for post
        $options = array(
            'http' => array(
                'header' => 'Content-Type: application/x-www-form-urlencoded\r\n'.
                    'Content-Length: '.strlen($requestQueryString).'\r\n',
                'method' => 'POST',
                'content' => $this->getPostData()
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

    }




} 