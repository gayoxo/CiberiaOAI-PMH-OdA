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


$Data=$_POST["selCombo"];

$Data2=$_POST["document"];

$DataJSon=array();

$DataOAI=array();

$DataDocu=array();

foreach ($Data as $key=>$valor)
{
	if (!empty($key)&&!empty($valor))
		$DataOAI[$key]=$valor;
}

foreach ($Data2 as $key=>$valor)
{
	if (!empty($key)&&!empty($valor))
		$DataDocu[$key]=$valor;
}

$DataJSon["Documents"]=$DataDocu;
$DataJSon["OAI"]=$DataOAI;

$DataJSonDec=json_encode($DataJSon);


echo '<meta http-equiv="Refresh" content="5;url=loadProcess.php?JsonCode='.urlencode($DataJSonDec).'">';
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