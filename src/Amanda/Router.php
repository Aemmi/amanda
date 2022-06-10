<?php

namespace Src\Amanda;

class Router{
    function __construct(){
        $this->full_path = $_SERVER['REQUEST_URI'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->request_method = $_SERVER['REQUEST_METHOD'];
        $this->indexedParams = array();
    }

    //convert incoming url to array
    public function url(){
        return explode('/', $this->full_path);
    }


    //check if request is query string
    public function isQueryString(){
        if($_SERVER['QUERY_STRING']){
            return true;
        }else{
            return false;
        }
    }

    //check if url contains query strings
    private function queryString(){
        $str = $this->url()[count($this->url())-(1)];
        parse_str(str_replace("?", "", $str),$get);
        $this->request = $get;
        return $this->request;
    }

    //get paramaters from query string
    public function params(){
        return $this->indexedParams;
    }

    private function strippedPath($path){
        return str_replace("/","",$path);
    }

    public function contain($p){
        if($this->isQueryString()){ 
            if(!isset($_GET[$p])){
                return false;
            }else{
                return true;
            }
        }else{
            if(!in_array($p,$this->params)){
                return false;
            }else{
                return true;
            }
        }
    }

    public function val($p){
        if($this->isQueryString() && $this->request_method == "GET"){ 

            return $_GET[$p];

        }elseif(!$this->isQueryString() && $this->request_method == "POST"){

            return $_POST[$p];

        }else{

            return $this->url()[array_search($p, $this->url())+1];

        }
    }

    public function handleRequest($path,$params){
        //print ($this->url()[2]);
        if($this->request_method == "GET"){

            //check if url has query string
            if($this->isQueryString()){ 
                //run query string method
                $url_comp = parse_url($this->full_path);
                parse_str($url_comp['query'], $params);
                
                if($this->url()[2] == $this->strippedPath($path)){
                    
                    $this->params = array_keys($this->queryString());
                    //print_r($this->params);
                    return true;
                }else{
                    return false;
                }

                // print($this->queryString());

            }else{
                if(empty($this->url())){
                    return true;
                }else{

                    if($this->url()[2] == $this->strippedPath($path)){
                        //print_r($this->url());
                        $this->params = $params;
                        //print_r($this->params);
                        return true;
                    }else{
                        return false;
                    }

                }
            }

        }else{
            //run post method
            if($this->url()[2] == $this->strippedPath($path)){
                //print_r($this->url());
                $this->params = $_POST;
                //print_r($this->params);
                return true;
            }else{
                return false;
            }
        }

    }

    public function get($path = '/', $params = null){
        return $this->handleRequest($path,$params);
    }

    public function post($path = '/', $params = null){ 
        return $this->handleRequest($path,$params);
    }

    public function render($file, $callback = null){
        if($callback != null){
            $callback();
        }
        include_once('temp/'.$file.'.temp.php');
    }
}