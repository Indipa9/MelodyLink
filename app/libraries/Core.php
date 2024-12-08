<?php
class Core{
//url format --> /controller/method /params
protected $currentController='Pages';
protected $currentMethod='index';
protected $param=[];

  public function __construct(){
//    print_r($this->getURL());//

 $url=$this->getURL();
   if(file_exists('../app/controllers/'.ucwords($url[0]).'.php')){
   //If the controller exists then laod it//
   $this->currentController=ucwords($url[0]);

   //unset the controller in the url
   unset($url[0]);
   //Call the controller
   require_once '../app/controllers/'.$this->currentController.'.php';

   //Instantiate the controller
   $this->currentController=new $this->currentController;

   //Check whether the method exists in the controller or not//
   if(isset($url[1])){
     //If the method exists then call it//
    if(method_exists($this->currentController, $url[1])){
        $this->currentMethod=$url[1];

        unset($url[1]);
 }
 }
//   echo $this->currentMethod;
//get parameter list//
$this->params=$url? array_values($url):[];

//call method and pass the parameter list
call_user_func_array([$this->currentController,$this->currentMethod],$this->params);
  }
 }
  public function getURL(){
 //     echo $_GET['url'];// 
 if(isset($_GET['url'])){
 $url=rtrim($_GET['url'],'/');
 $url=filter_var($url,FILTER_SANITIZE_URL);
 $url=explode('/',$url);

 return $url;

 }
  }

 }

 ?>