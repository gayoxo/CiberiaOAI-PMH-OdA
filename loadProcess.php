<?php 
include_once(getcwd()."/include.php");
$titulopaginabo="Menu de Exportacion/Actualización";
$explicaciontitulopaginabo="Menu de proceso de exportacion y actualización en XLS";
$visit->options->seccion = "Exportacion/Actualización";
$visit->options->subseccion = "Exportacion/Actualización";
include_once(getcwd()."/bo_top.php");
include_once("config.php");
?>

<?php


$DataJSonDec=$_GET["JsonCode"];

echo $DataJSonDec;
echo "<br>";
var_dump($DataJSonDec);


exit();

$ServerService='http://'.ClavyServer.':'.ClavyPort.'/'.ClavyDomine.'/rest/OAIPMH/';


$service_url = $ServerService.'importOda2?user='.TZN_DB_EXTERNAL_USER.'&password='.TZN_DB_EXTERNAL_PASS.'&server='.TZN_DB_EXTERNAL_HOST.'&database='.TZN_DB_BASE.'&port=3306&url=http://'.TZN_ODA_HOST.'/'.APP_NAME.'&convert=true&userclavy='.Clavyuser.'&passwordclavy='.Clavyuserkey.'&KeyClavy='.Clavykey;
$curl = curl_init($service_url);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
	echo "<br>";
	echo 'El sistema Clavy en http://'.ClavyServer.':'.ClavyPort.' ha dado un error en la carga, por favor intentelo de nuevo';
	echo "<br>";
	echo $curl_response;
	echo "<br>";
	
}else
{
$status = curl_getinfo($curl);
curl_close($curl);
if ($status['http_code']=='200')
{
echo "<br>";
echo $curl_response;
echo "<br>";
echo "<br>";
echo "<input type=\"button\" name=volver onclick=\"document.location.href= 'index.php';\" value=\"Volver al menu de Import/Export\">";
}
else
{
	echo "<br>";
	echo 'Error: '.$status['http_code'];
	echo "<br>";
	echo 'El sistema Clavy en http://'.ClavyServer.':'.ClavyPort.' ha dado un error en la carga, lea informacion extra y por favor intentelo de nuevo';
	echo "<br>";
	echo "<br>";
	echo "Log de Informacion:";
	echo "<br>";
	echo $curl_response;
	echo "<br>";
	
}
}

?>

<?php
include_once(getcwd()."/bo_bottom.php");
?>