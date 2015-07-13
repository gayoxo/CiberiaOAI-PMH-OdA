<?php 
include_once(getcwd()."/include.php");
$titulopaginabo="Menu de Generacion De Informes";
$explicaciontitulopaginabo="Menu de Actualizacion y despliegue de OAI-PMH";
$visit->options->seccion = "Informes";
$visit->options->subseccion = "Informes";
include_once(getcwd()."/bo_top.php");
include_once("config.php");
?>

<?php

$id=$visit->options->usuario->id;
$rol=$visit->options->usuario->rol;

if ($rol==A)
	$Admin=true;
else
	$Admin=false;


//Fase 1 Revision del sistema, si esta activo
$ServerService='http://'.ClavyServer.':'.ClavyPort.'/'.ClavyDomine.'/rest/OAIPMH/';
$service_url = $ServerService.'active';
$curl = curl_init($service_url);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
	echo 'El sistema Clavy en http://'.ClavyServer.':'.ClavyPort.' no esta activo, por favor pruebe de nuevo y contacte con el administrador si persiste';
  
}else
{

$IsLoadCollection=false;

//Fase 2 Revision de carga de la coleccion
$service_url = $ServerService.'loadstatusoda2?KeyClavy='.Clavykey;
$curl = curl_init($service_url);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
	echo 'El sistema Clavy en '.$Server.' no esta activo, por favor pruebe de nuevo y contacte con el administrador si persiste';
  
}else
{
echo "<p>Estado de carga en el OAI-PMH</p>";
//fase 2 Correcta
$status = curl_getinfo($curl);
curl_close($curl);
if ($status['http_code']=='403'||$status['http_code']=='500'||$status['http_code']=='501')
	{
	echo $curl_response;

	}
	else{ 
		if ($status['http_code']=='404')
		{

		if ($Admin)
			echo "<input type=\"button\" id=\"importB\" name=Import1 onclick=\"document.location.href= 'preload.php';\" value=\"Cargar coleccion al sistema de OAI-PMH\">";
		}
		else{
			if ($status['http_code']=='200')
			{
			echo "<p> Fecha de la ultima actualizacion o carga : ".$curl_response."</p>";
			}

			if ($Admin)
			{
					$fileName = 'save/ultima.json';
					if ( file_exists($fileName) && ($fp = fopen($fileName, "rb"))!==false ) {
					echo "<br>";
					echo "<input type=\"button\" id=\"importB\" name=Import1 onclick=\"document.location.href= 'preload.php';\" value=\"Actualizar de nuevo la coleccion al sistema OAI-PMH \">";

					fclose($fp);
					}
					else {
						echo "<input type=\"button\" id=\"importB\" name=Import1 onclick=\"document.location.href= 'preload.php';\" value=\"Actualizar de nuevo la coleccion al sistema OAI-PMH\">";
					}
			}
			}
		}
	
	
}
echo "<br>";
echo "<br>";
echo "<br>";


}
 ?>

<?php
include_once(getcwd()."/bo_bottom.php");
?>