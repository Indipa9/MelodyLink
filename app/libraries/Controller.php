<?php 

class Controller
{

	public function model($model)
	{
		$filename = "../app/models/".$model.".php";
		if(file_exists($filename))
		{
			require $filename;
		}else{

			$filename = "../app/models/404.view.php";
			require $filename;
		}
	}
	public function view($view){
		if(file_exists("../app/views/".$view.".php"))	
		{
			require "../app/views/".$view.".php";
		}
		else{
			$filename = "../app/views/404.view.php";
			require $filename;
		}


	}	
}