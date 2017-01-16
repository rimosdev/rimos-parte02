<?php
require 'vendor/autoload.php';
$app = new \Slim\Slim();

$app->post('/list', function () use ($app) {

    $config['base_url'] =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https" : "http");
	$config['base_url'] .=  "://".$_SERVER['HTTP_HOST'];
	$config['base_url'] .=  str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

  	//validating if is POST
  	$q = $app->request->post('q');
    $listEmp = json_decode(file_get_contents('employees.json'));
  	if(trim($q) != '')
  	{

	    //filtering for email
	    $ret = array();
	    foreach ($listEmp as $key => $empleado) {
	    	if(strstr($empleado->email, $q))
	    		$ret[] = $empleado;
	    }
	    $listEmp = (object) $ret;
	}

    $app->render('employees_list', compact('config','listEmp','q'));
});

$app->get('/list', function () use($app) {
  $config['base_url'] =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https" : "http");
  $config['base_url'] .=  "://".$_SERVER['HTTP_HOST'];
  $config['base_url'] .=  str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

    $listEmp = json_decode(file_get_contents('employees.json'));
    $app->render('employees_list', compact('config','listEmp'));
});
$app->get('/detail/:id', function ($idEmp) {
    $listEmp = json_decode(file_get_contents('employees.json'));
    foreach ($listEmp as $key => $empleado) {
    	echo $empleado->name. '<br>';
    	echo $empleado->email. '<br>';
    	echo $empleado->position. '<br>';
    	echo $empleado->salary. '<br>';
    }
});
$app->run();