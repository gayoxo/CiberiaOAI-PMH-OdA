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


$fileName = 'save/ultima.json';
if ( file_exists($fileName) && ($fp = fopen($fileName, "rb"))!==false ) {
		$DataJSonDec= fgets($fp);
		 fclose($fp);
		 echo '<meta http-equiv="Refresh" content="5;url=loadProcess.php?JsonCode='.urlencode($DataJSonDec).'">';
}
else {
		echo "<input type=\"button\" id=\"importB\" name=Import1 onclick=\"document.location.href= 'preload.php';\" value=\"Actualizar de nuevo la coleccion al sistema OAI-PMH\">";
		echo '<meta http-equiv="Refresh" content="5;url=load.php">';
	}

$DataJSonDec=json_encode($DataJSon);



echo "<br>";
echo '<div align="center">
 <p>Carga en proceso, por favor espere</p>
 <br>
 <img src="barOda.gif" alt="loding"> </div>';
echo "<br>";


?>

<?php
include_once(getcwd()."/bo_bottom.php");
?>