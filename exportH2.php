<?php 
include_once(getcwd()."/include.php");
$titulopaginabo="Menu de Generacion De Informes";
$explicaciontitulopaginabo="Menu de Generacion De Informes en formato paginas HTML";
$visit->options->seccion = "Informes";
$visit->options->subseccion = "Informes";
include_once(getcwd()."/bo_top.php");
include_once("config.php");
?>

<?php 
function Conectarse() 
{ 
   if (!($link=mysql_connect(TZN_DB_EXTERNAL_HOST,TZN_DB_EXTERNAL_USER,TZN_DB_EXTERNAL_PASS))) 
   { 
      echo "Error conectando a la base de datos."; 
      exit(); 
   } 
   if (!mysql_select_db(TZN_DB_BASE,$link)) 
   { 
      echo "Error seleccionando la base de datos."; 
      exit(); 
   } 
   mysql_query("SET NAMES 'utf8'");
   return $link; 
}
?>

<?php


$id=$visit->options->usuario->id;
$rol=$visit->options->usuario->rol;

if ($rol==A)
	$Admin="true";
else
	$Admin="false";

$link=Conectarse(); 
   $result=mysql_query("SELECT idov FROM permisos WHERE idusuario=".$id,$link); 

   $finalStruc="";
   $coma=false;
   while($row = mysql_fetch_array($result)) {
	   $ValueIdov=$row["idov"];
		if ($coma)
			$finalStruc=$finalStruc.',';
		else $coma=true;			
		
		$finalStruc=$finalStruc.$ValueIdov;
   }
mysql_free_result($result); 
mysql_close($link); 




$documentos=$_REQUEST['document'];
//var_dump($documentos);
if (empty($documentos))
	{
		echo '<meta http-equiv="Refresh" content="5;url=exportH1.php">';
		echo 'Lista de Documentos vacia <i>(Seras redirigido automaticamente a la pagina anterior)</i><br>';
	}
else
{
$arrlength = count($documentos);

for($x = 0; $x < $arrlength; $x++) {
	if ($x!=0)
		$finalDocs=$finalDocs.',';
	$finalDocs=$finalDocs.$documentos[$x];
}
}



$estructura=$_REQUEST['estructura'];
//var_dump($estructura);
if (!empty($estructura))
	{
		$arrlength = count($estructura);

for($x = 0; $x < $arrlength; $x++) {

	if ($x!=0)
		$finalStruc=$finalStruc.',';
	$finalStruc=$finalStruc.$estructura[$x];
}
	}


$finalDocsAllow="";


$ServerService='http://'.ClavyServer.':'.ClavyPort.'/'.ClavyDomine.'/rest/service/';
$service_url = $ServerService.'exportOdaHTML?userclavy='.Clavyuser.'&passwordclavy='.Clavyuserkey.'&KeyClavy='.Clavykey.'&Admin='.$Admin;

$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST,           1 );
curl_setopt($curl, CURLOPT_POSTFIELDS,'&DocList='.$finalDocs.'&DoclistAdmin='.$finalDocsAllow.'&StructList='.$finalStruc); 
curl_setopt($curl, CURLOPT_HTTPHEADER,     array('Content-Type: application/x-www-form-urlencoded')); 
$curl_response = curl_exec($curl);

if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
	echo 'El sistema Clavy en '.$Server.' no esta activo, por favor pruebe de nuevo y contacte con el administrador si persiste';
  
}else
{
	echo $curl_response;
	echo "<br>";
	echo "<input type=\"button\" name=volver onclick=\"document.location.href= 'index.php';\" value=\"Volver al menu de Import/Export\">";
}
?>

<?php
include_once(getcwd()."/bo_bottom.php");
?>