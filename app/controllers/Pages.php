<?php
class Pages extends Controller{
public function __construct(){
   // echo 'this is the pages controller';
$this->pagesModel=$this->model('m_Pages');
}
public function index(){



}
public function about(){
    $users=$this->pagesModel->getUsers();
$data=[
    'users'=>$users
];

$this->view('v_about',$data);

}


}
?>