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

function funcionCompleta($idpadre,$link,$inicial) 
{ 
$result=mysql_query("SELECT id, nombre FROM section_data WHERE idpadre=".$idpadre." ORDER BY id",$link);
while($row = mysql_fetch_array($result)) {
		if (strlen($row["nombre"])>62)
			$Descipcion=utf8_encode(substr (utf8_decode($row["nombre"]),0,60)."...");
		else
			$Descipcion=$row["nombre"];
		$Descipcion=utf8_encode(strip_tags(utf8_decode($Descipcion))); 
		
		if ($inicial)
			echo "<li>".$Descipcion;
		else
			echo "<li><input type=\"checkbox\" name=\"estructura[]\" value=\"".$row["id"]."\"> ".$row["id"]." : ".$Descipcion;
	  echo "<ul>";
	  funcionCompleta($row["id"],$link,false); 
	  echo "</ul></li>";
     // printf("<tr><td>&nbsp;%s</td><td>&nbsp;%s&nbsp;</td></tr>", $row["id"],$row["value"]); 
   }
mysql_free_result($result); 
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
	
	
echo "<form id=\"formdata\" name=\"formdata\" action=\"exportH2.php\" method=\"POST\">";
echo "<p><b>Objeto Digital </b> <font style=\"color:#FF0000;\"><i>(Aviso: No se permite una selecciona vacia)</i> </font></p>";
$link=Conectarse(); 
   
 

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
	  echo "<input type=\"checkbox\" name=\"document[]\" value=\"".$row["id"]."\"> ".$row["id"]." : ".$Descipcion."<br>";
     // printf("<tr><td>&nbsp;%s</td><td>&nbsp;%s&nbsp;</td></tr>", $row["id"],$row["value"]); 
   }
mysql_free_result($result); 
 
echo "<br>";
echo "<p><b>Estructuras </b> <font style=\"color:#FF0000;\"><i>(Aviso: Cuando seleccione un campo debe selccionar todos sus campos padre, si los tubiera)</i> </font></p>"; 

echo '<button type="button" onClick="checkAll(\'estructura[]\', true);" >Seleccionar todo</button>';
echo '&nbsp;&nbsp;';
echo '<button type="button" onClick="checkAll(\'estructura[]\', false);" >Deseleccionar todo</button>';
echo '<br>';
   funcionCompleta(0,$link,true);
   
  
 mysql_close($link); 
 

echo "<input type=\"button\" onclick=\"preUpload();\" id=\"submitButton\" value=\"Exportar a HTML\" name=\"Exportar a HTML\" >";
echo "</form>";


?>

<script>function preUpload(){ 
document.getElementById('submitButton').disabled = true;
document.getElementById('formdata').submit();
}</script>

<?php
include_once(getcwd()."/bo_bottom.php");
?>