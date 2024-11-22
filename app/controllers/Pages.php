<?php   
    class Pages extends Controller{
        public function __construct(){
            //echo "Pages loaded";
        }
        public function index(){
            echo "Index";
        }
        public function about($name){
            $data = [
                'title' => 'About Us',
                'name' => $name
            ];
            $this->view('artists', $data);
        }
    }    
    
?>