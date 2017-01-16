<?php
require 'vendor/autoload.php';
$app = new \Slim\Slim();

//Fyunction for search by EMAIL
$app->post('/', function () use ($app) {

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

//Fyunction for list employees
$app->get('/', function () use($app) {
  $config['base_url'] =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https" : "http");
  $config['base_url'] .=  "://".$_SERVER['HTTP_HOST'];
  $config['base_url'] .=  str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

    $listEmp = json_decode(file_get_contents('employees.json'));
    $app->render('employees_list', compact('config','listEmp'));
});

//Fyunction for detail of employee
$app->get('/detail/:id', function ($idEmp) use ($app){
    $config['base_url'] =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ?  "https" : "http");
	$config['base_url'] .=  "://".$_SERVER['HTTP_HOST'];
	$config['base_url'] .=  str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

    $listEmp = json_decode(file_get_contents('employees.json'));

    $empleado = '';

    if(trim($idEmp) != '')
  	{
	    foreach ($listEmp as $key => $emp) {
	    	if($idEmp == $emp->id)
	    		$empleado = $emp;
	    }
	}

    $app->render('employees_detail', compact('config','empleado'));
});
$app->run();

//function for RESTFUL
$app->post('/api', function () use ($app) {

  	//validating if is POST
  	$min_salary = $app->request->post('min_salary');
  	$max_salary = $app->request->post('max_salary');
    $listEmp = json_decode(file_get_contents('employees.json'));
  	if(trim($q) != '')
  	{
	    //filtering for email
	    $ret = array();
	    foreach ($listEmp as $key => $empleado) {
	    	if($empleado->salary >=$min_salary and $empleado->salary<=$max_salary)
	    		$ret[] = $empleado;
	    }
	    $listEmp = (object) $ret;
	}

    echo json_encode($listEmp);
});