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
//function for RESTFUL
$app->post('/api/', function () use ($app) {

  	//validating if is POST
  	$min_salary = $app->request->post('min_salary');
  	$max_salary = $app->request->post('max_salary');
    $listEmp = json_decode(file_get_contents('employees.json'));
    $ret = array();

$xml = new SimpleXMLElement('<root/>');

  	if(isset($min_salary) and isset($max_salary) and intval($min_salary) >=0 and intval($max_salary)>=0)
  	{
	    //filtering for email
	    $ret = array();
	    foreach ($listEmp as $key => $empleado) {
		    //get sallary in number format
		    $sallary = preg_replace('/[^\d,\.]/', '', $empleado->salary);
		    $sallary = preg_replace('/,(\d{2})$/', '.$1', $sallary);
		    $sallary = str_replace(',', '', $sallary);
	    	if($sallary >=$min_salary and $sallary<=$max_salary)
	    	{
	    		$add = array(
	    						'id' => $empleado->id,
	    						'name' => $empleado->name,
	    						'email' => $empleado->email,
	    						'salary' => $empleado->salary
	    					);
	    		$ret[] = $add;
	    		array_walk_recursive($add, array ($xml, 'addChild'));
	    	}
	    }
	}

// creating object of SimpleXMLElement
$xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
   array_to_xml($ret,$xml_data);

print $xml_data->asXML();
    // echo $xml;
});

function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_numeric($key) ){
            $key = 'item'.$key; //dealing with <0/>..<n/> issues
        }
        if( is_array($value) ) {
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
     }
}


$app->run();