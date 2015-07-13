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

function funcionCompleta($idpadre,$link,$inicial,$TabklaIds) 
{ 

$result=mysql_query("SELECT id, nombre, tipo_valores FROM section_data WHERE idpadre=".$idpadre." ORDER BY id",$link);
while($row = mysql_fetch_array($result)) {
		if (strlen($row["nombre"])>62)
			$Descipcion=utf8_encode(substr (utf8_decode($row["nombre"]),0,60)."...");
		else
			$Descipcion=$row["nombre"];
		$Descipcion=utf8_encode(strip_tags(utf8_decode($Descipcion))); 
		
		if ($inicial)
			echo "<li>".$Descipcion;
		else
		{
			echo "<li>";
			if ($row["id"]=="111")
				echo $Descipcion. " = dc:description";
			else
			{
				echo $Descipcion;
			if (!is_null($row["tipo_valores"])&&$row["tipo_valores"]!="X")	
				{
				echo " = ";
				ComboDC($row["id"],$TabklaIds);
				}
			}
			//echo "<li>  <input type=\"checkbox\" name=\"estructura[]\" value=\"".$row["id"]."\"> ".$Descipcion;
		}
	  echo "<ul>";
	  funcionCompleta($row["id"],$link,false,$TabklaIds); 
	  echo "</ul></li>";
     // printf("<tr><td>&nbsp;%s</td><td>&nbsp;%s&nbsp;</td></tr>", $row["id"],$row["value"]); 
   }
mysql_free_result($result); 
}


function ComboDC($idrow,$TabklaIds)
{
	
	$Select=$TabklaIds[$idrow];
	
	/*if (!is_null($Select))
		var_dump($Select);*/
	
	echo "<SELECT NAME=\"selCombo[".$idrow."]\" >"; 
	echo "<OPTION VALUE=\"\"></OPTION>";
	
	$array = array("dc:title", "dc:creator","dc:contributor","dc:date","dc:language","dc:publisher","dc:relation","dc:format","dc:type","dc:description","dc:identifier","dc:source","dc:subject","dc:coverage","dc:rights");
	foreach ($array as $valor)
	{
		echo "<OPTION VALUE=\"".$valor."\"";
		if (!is_null($Select)&&$valor==$Select)
			echo "selected";
		echo ">".$valor."</OPTION>";
	}	
	
	
	
	/*echo "<OPTION VALUE=\"dc:title\"";
	echo ">dc:title</OPTION>";
	
	echo "<OPTION VALUE=\"dc:creator\"";
	echo ">dc:creator</OPTION>";
	
	echo "<OPTION VALUE=\"dc:contributor\"";
	echo ">dc:contributor</OPTION>";
	
	echo "<OPTION VALUE=\"dc:date\"";
	echo ">dc:date</OPTION>";
	
	echo "<OPTION VALUE=\"dc:language\"";
	echo ">dc:language</OPTION>";
	
	echo "<OPTION VALUE=\"dc:publisher\"";
	echo ">dc:publisher</OPTION>";

	echo "<OPTION VALUE=\"dc:relation\">dc:relation</OPTION>";

	echo "<OPTION VALUE=\"dc:format\">dc:format</OPTION>";
	
	echo "<OPTION VALUE=\"dc:type\">dc:type</OPTION>";
	
	echo "<OPTION VALUE=\"dc:description\">dc:description</OPTION>";
	
	echo "<OPTION VALUE=\"dc:identifier\">dc:identifier</OPTION>";
	
	echo "<OPTION VALUE=\"dc:source\">dc:source</OPTION>";
	
	echo "<OPTION VALUE=\"dc:subject\">dc:subject</OPTION>";
	
	echo "<OPTION VALUE=\"dc:coverage\">dc:coverage</OPTION>";
	
	echo "<OPTION VALUE=\"dc:rights\">dc:rights</OPTION>";
	*/
	
	
	echo "</SELECT>"; 
}

?>


<script type="text/javascript" language="javascript">// <![CDATA[
function checkAll(formname, checktoggle)
{
  var checkboxes = new Array(); 
  checkboxes = document.getElementsByName(formname) ;
 
  for (var i=0; i<checkboxes.length; i++)  {
    if (checkboxes[i].type == 'checkbox')   {
      checkboxes[i].checked = checktoggle;
    }
  }
}
// ]]></script>


<?php
	
	


try{

   $fileName = 'save/ultima.json';
if ( file_exists($fileName) && ($fp = fopen($fileName, "rb"))!==false ) {
		$DataJSonDec= fgets($fp);
		 fclose($fp);
		 $DataDec=json_decode($DataJSonDec, true);
		 
		 
		 $CamposData=$DataDec["OAI"];
		
		 $CamposDoc=$DataDec["Documents"];
			
		$CamposDoc2=array();	
			
			foreach ($CamposDoc as $esta )
				$CamposDoc2[$esta]="true";

		/* var_dump($CamposData);
		  echo "<br>";
		 var_dump($CamposDoc);
		  echo "<br>";
		 var_dump($CamposDoc2);*/
		 
}

}

catch(Exception $e){

   echo $e->getMessage();

}
	
	
	
$id=$visit->options->usuario->id;
$rol=$visit->options->usuario->rol;

if ($rol==A)
	$Admin=true;
else
	$Admin=false;

if ($rol==C)
	$User=true;
else
	$User=false;
	
	
echo "<form id=\"formdata\" name=\"formdata\" action=\"load.php\" method=\"POST\">";



echo "<br>";
echo "<p><b>Estructuras </b></p>"; 


$link=Conectarse(); 
 
   funcionCompleta(0,$link,true,$CamposData);
   
  


 
if ($Admin)
   $result=mysql_query("SELECT A.id, B.value FROM virtual_object AS A, text_data AS B WHERE A.id=B.idov AND B.idseccion=111 ORDER BY A.id",$link); 
else if ($User)
	$result=mysql_query("SELECT A.id, B.value FROM virtual_object AS A, text_data AS B WHERE A.id=B.idov AND B.idseccion=111 AND A.ispublic='S' ORDER BY A.id",$link); 
else
	{
	$result2=mysql_query("SELECT idov FROM permisos WHERE idusuario=".$id,$link); 
	$finalStruc="";
   $coma=false;
   while($row = mysql_fetch_array($result2)) {
	   $ValueIdov=$row["idov"];
		if ($coma)
			$finalStruc=$finalStruc.',';
		else $coma=true;			
		
		$finalStruc=$finalStruc.$ValueIdov;
   
   }
   
   if (!empty($finalStruc))
	$result=mysql_query("SELECT A.id, B.value FROM virtual_object AS A, text_data AS B WHERE A.id=B.idov AND B.idseccion=111 AND (A.id IN (".$finalStruc.") OR A.ispublic='S') ORDER BY A.id",$link); 
	else
		$result=mysql_query("SELECT A.id, B.value FROM virtual_object AS A, text_data AS B WHERE A.id=B.idov AND B.idseccion=111 AND A.ispublic='S' ORDER BY A.id",$link); 
	

	}

	
echo "<br>";
echo "<br>";
echo "<p><b>Documentos</b></p>"; 
	
	
	echo '<button type="button" onClick="checkAll(\'document[]\', true);" >Seleccionar todo</button>';
echo '&nbsp;&nbsp;';
echo '<button type="button" onClick="checkAll(\'document[]\', false);" >Deseleccionar todo</button>';
echo '<br>';
  
while($row = mysql_fetch_array($result)) {
		if (strlen($row["value"])>62)
			$Descipcion=utf8_encode(substr (utf8_decode($row["value"]),0,60)."...");
		else
			$Descipcion=$row["value"];
		
		$Descipcion=utf8_encode(strip_tags(utf8_decode($Descipcion))); 
		
		
		$Select=$CamposDoc2[$row["id"]];
	  
	/*  var_dump ($Select);*/
	  
		
	  echo "<input type=\"checkbox\" name=\"document[]\" value=\"".$row["id"]."\"";
	  
	  
	  if (!is_null($Select))
		echo " checked ";
	  
	  echo "> ".$row["id"]." : ".$Descipcion."<br>";
     // printf("<tr><td>&nbsp;%s</td><td>&nbsp;%s&nbsp;</td></tr>", $row["id"],$row["value"]); 
   }
mysql_free_result($result); 


 mysql_close($link); 

echo "<br>";
echo "<br>";	
	
echo "<input type=\"button\" onclick=\"preUpload();\" id=\"submitButton\" value=\"Actualiza/Carga OAI-PMH\" name=\"Actualiza/Carga OAI-PMH\" >";
echo "</form>";

?>

<script>function preUpload(){ 
document.getElementById('submitButton').disabled = true;
document.getElementById('formdata').submit();
}</script>

<?php
include_once(getcwd()."/bo_bottom.php");
?>