<?php
//********************************************************
//*********************************************************read data


$n=0;
$p=array();

if (($handle = fopen("wk.csv", "r")) !== FALSE) {
	
	while (($data = fgetcsv($handle, 0, "\t")) !== FALSE) {
		//$data[0]=strip_tags($data[0]);
		
		$latbool=true;
		
		//$geodaten=explode(" ", $data[0]);
		$nummer=$data[0];
		$latitude=$data[7];
		$pos1 = stripos($latitude, ".");
		if($pos1===false)
		{
			$latbool=false;
		}


		$longitude=$data[6];
		$pos2 = stripos($longitude, ".");
		if($pos2===false)
		{
			$latbool=false;
		}
		$long1=substr($longitude, 0,1);
$long2=substr($longitude,1);
$longitude_neu=$long1 . "." . $long2;
	
	$regiment=trim($data[3]);
	$place=trim($data[4]);
	
	$loss=trim($data[5]);
$beschreibung=$regiment . "\n" . $loss;

$kat = explode(",", $data[4]);
if(isset($kat) && $kat[1]!=="" && count($kat)>1)
{
$kategorie=trim($kat[1]);
}
else
{
	
}

$ort=$data[4];

$titel1=addslashes($data[1]);
$titel2=addslashes($data[2]);
$titel=$titel1 . ", " . $titel2;

		if($n>=0 && trim($latitude)!=="" && $latbool==true)
		$p[] = array("nummer"=>$nummer, "latitude"=>$latitude,"longitude"=>$longitude, "beschreibung"=>$beschreibung, "titel"=>$titel,  "ort"=>$ort, "kategorie"=>$kategorie);
		 

		$n++;
		$kat[1]="";
	}
	fclose($handle);
}
$_SESSION['p']=$p;
//**********************************
$jsonData = json_encode($p);
echo  $jsonData."\n";
?>
