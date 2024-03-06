<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>Uppladdade filer</title>
<style type="text/css">
	body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; }
	h1 {font-size: 18px; letter-spacing: 1px; font-weight: normal;}
	
	table {
		font-family:Verdana, Geneva, sans-serif;
		font-size: 13px;	
		border-collapse: collapse;
	 	
	}
	table, th, td {
  		border: 1px solid black;
	}
	th, td {
  		height: 25px;
		padding: 5px;
	}
	tr:nth-child(even){
		background-color:lightgrey;
	}
	tr:hover {
		background-color: lightblue;
	}
	th {
		background-color:grey;
		color: #FFFFFF;
	}
</style>
</head>
<body>
<h1>Lista- Uppladdade filer</h1>
<hr>
<p>Största tillåtna filstorlek är 500 kB, 800 * 600 pixlar</p>
<?php
#namn på mappen dit filerna sparas
$upload_dir = 'files/';

#skapar mappen om den inte redan finns
if(!file_exists($upload_dir)) {
	mkdir($upload_dir, 0755);
	chmod($upload_dir, 0755);
}

#Tabellista över filer med download-länkar


#kontrollerar om mappen öppnats och skriver ut innehållet
if ($dir_list = opendir($upload_dir)) {
	echo '<table border="0" cellspacing="0" cellpadding="5">';
	echo '<tr>';
	echo '<th>Filnamn</th>';
	echo '<th>Storlek</th>';
	echo '<th>Datum</th>';
	echo '<th>Tid</th>';
	echo '</tr>';
	while (false !== ($filename = readdir($dir_list))) {
	  #hoppar över filnamnen "." och ".."
	  if ($filename != "." && $filename != "..") {
	   	echo '<tr>';
     	echo '<td><a href="download.php?file='.$filename.'">'.$filename.'</a></td>';
		echo '<td>'.filesize($upload_dir.$filename).' bytes</td>';
		echo '<td>'.date ("d.m.Y", filemtime($upload_dir.$filename)).'</td>';
		echo '<td>'.date ("H:i:s", filemtime($upload_dir.$filename)).'</td>';
	  }
	}
	echo '</table>';
	closedir($dir_list);
}

?>
<br>
<form method="post" action = "upload.php">
    <input type="submit" value="Lägg till ny fil">
</form>

</body>
</html>