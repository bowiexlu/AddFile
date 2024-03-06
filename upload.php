<?php

ob_start();

# om submitknappen används i formuläret
if(isset($_POST['submit'])) {

	
	#Minnesstorlek, som måste höjas för stora filer
	$memory = ini_set("memory_limit", "32M");
	
	
	#Maxstorlek på filer (extra kontroll)
	$extra_max_file_size = 500000;
	
	#Bredd för bildfiler
	$max_width = '800';
	
	#Höjd för bildfiler
	$max_height = '600';
	
	#Namn på mappen där filen ska sparas
	$upload_dir = 'files/';
	
		
	#Skapar mappen om den inte redan finns, vilket den borde göra 
	if(!file_exists($upload_dir)){
		mkdir($upload_dir, 0755);
		chmod($upload_dir, 0755);
	}
	
	# Funktion som trimmar filnamnet tar bort bl.a. åäö och konstiga mapper
	function trim_filename($string){
		$string = ltrim($string);
		$string = preg_replace( "/ +/", " ", strtolower($string) );
		$string = str_replace(' - ','-', $string);
		$string = str_replace(array('%',';','/','?',':','@','&','=','+','$',',','#','(',')'),'', $string);
		$search = array(' ','Å','Ä','Ö','å','ä','ö','ü','ë','ï','é','è','à','ç');
		$replace = array('_','a','a','o','a','a','o','u','e','i','e','e','a','c');
		$string = str_replace($search, $replace, $string);
		$string = preg_replace("/[^a-z0-9._-]/", "", $string);
		$string = strtolower($string);
		return urlencode($string);
	}

	# Justerar och sparar filnamnet i variabel
	$filnamn = trim_filename($_FILES['upload_file']['name']);

	# Funktion som kontrollerar och skriver ut felkoder från PHP
	function error_message($error_code) {
		switch ($error_code) {
			case UPLOAD_ERR_INI_SIZE:
				return 'Filstorleken är större än villkoret för "upload_max_filesize" i php.ini (som nu är '
				.ini_get('upload_max_filesize').')<br>Observera att "upload_max_filesize" inte får överstiga "post_max_size" 
				(som nu är '.ini_get('post_max_size').')<br>Välj en fil med mindre storlek eller kontakta administratören!';
			case UPLOAD_ERR_FORM_SIZE:
				return 'Filstorleken är större än tillåten storlek. <br>
				Välj en fil med mindre storlek eller kontakta administratören!<br>
				(Filstorleken är större än villkoret för "MAX_FILE_SIZE").';
			case UPLOAD_ERR_PARTIAL:
				return 'Filen kunde bara delvis laddas upp.';
			case UPLOAD_ERR_NO_FILE:
				return 'Ingen fil har valts eller den valda filen kunde inte laddas upp.';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Det finns inte eller kan inte skapas någon temporär mapp för filen.';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Det gick inte att skriva filen till disk.';
			case UPLOAD_ERR_EXTENSION:
				return 'Filuppladdningen stoppad pga fel filtyp.';
			default:
			return 'Okänt fel stoppade uppladdning av filen.';
		}
	}

	# kontrollerar den (extra) maximala filstorleken
	if($_FILES["upload_file"]["size"] > $extra_max_file_size){
		echo '<br>';
		echo 'Felmeddelande:<br>';
		echo 'Filstorleken <b>'.($_FILES['upload_file']['size']).'</b>byte är större än tillåten storle<b>'.$extra_max_file_size.'</b> byte.<br> Välj en fil med mindre storlek eller kontakta administratören!';
		echo'<br><br><a href="javascript:history.go(-1)">tillbaka</a>';
		exit();
	}

	# EXTRA FÖR BILDER: kontrollerar bredd och höjd
	list($width, $height) = getimagesize($_FILES['upload_file']['tmp_name']);
	if($width > $max_width || $height > $max_height){
		echo '<br>';
		echo 'Felmeddelande:<br>';
		echo 'Bildens storlek <b>'.$width.'x'.$height.'px</b> är större än tillåten storlek <b>'.$max_width. 'x'.$max_height.'px</b>.<br>
		Välj en fil med mindre storlek!';
		echo'<br><br><a href="javascript:history.go(-1)">tillbaka</a>';
		exit();
	}

	# Felmeddelande visas om PHP returnerar felkoder
	if($_FILES['upload_file']['error'] > 0) {
		echo '<br>';
		echo 'Felmeddelande:<br>';
		echo '<b>Felkod '.$_FILES['upload_file']['error'].':</b><br>';
		echo error_message($_FILES['upload_file']['error']).'';
		echo'<br><br><a href="javascript:history.go(-1)">tillbaka</a>';
		exit();
		
	}



	// kontrollerar om filen redan finns
	if (file_exists($upload_dir.$filnamn)) {
		echo '<br>';
		echo 'Felmeddelande:<br>';
		echo 'Det finns redan en fil med namnet <b>'.$filnamn.'</b> i mappen <b>'.$upload_dir.'</b>';
		echo'<br><br><a href="javascript:history.go(-1)">tillbaka</a>';
		exit();
	}

	# kontrollerar MIME (Multipurpose Internet Mail Extension) för filen
	if ((($_FILES['upload_file']['type'] == 'image/gif')
		|| ($_FILES['upload_file']['type'] == 'image/png')
		|| ($_FILES['upload_file']['type'] == 'image/jpeg')
		|| ($_FILES['upload_file']['type'] == 'image/pjpeg')
		|| ($_FILES['upload_file']['type'] == 'application/pdf')
		|| ($_FILES['upload_file']['type'] == 'text/plain')
		|| ($_FILES['upload_file']['type'] == 'application/msword')
		|| ($_FILES['upload_file']['type'] == 'application/vnd.ms-excel')
		))
	{

		# Laddar upp bilden om filens MIME är godkänd
		move_uploaded_file($_FILES['upload_file']['tmp_name'],$upload_dir.$filnamn);
		
		header("Location:index.php");
	
	
		# Sparar information i loggfil
		$ip = $_SERVER['REMOTE_ADDR'];
		$datum = date('d.m.Y');
		$time_local = date('H:i:s');
		$loggfil = fopen($upload_dir.'../upload_log.txt', 'a+');
		if($loggfil){
			fwrite($loggfil, "Filname: ".$filnamn." | Datum: ".$datum ." | Tid: ".$time_local." | IP-nummer:".$ip."\r\n");
			fclose($loggfil);
		}
	}else{
	#Om filens MIME inte är godkänd
		echo '<br>';
		echo 'Felmeddelande:<br>';
		echo 'Filtypen du valt är inte tillåten!';
		echo '<br><br><a href="javascript:history.go(-1)">tillbaka</a>';
	}

}else {

	# Formulär för uppladdning
	echo '<h3>Ladda upp fil</h3>';
	echo '<hr>';
	echo '<form id="uploadform" name="uploadform" method="post" action="" enctype="multipart/form-data">';
		echo '<fieldset style="width: 500px; padding: 5px;">';
			echo '<label for="upload_file">Fil som ska laddas upp:</label><br>';
			echo '<input type="hidden" name="MAX_FILE_SIZE" value="1000000" >';
			echo '<input type="file" name="upload_file" id="upload_file" size="40"><br><br>';
			echo '<input type="submit" name="submit" value="Ladda upp!" >';
			echo '<input type="reset" name="reset" id="button" value=" Återställ ">';
		echo '</fieldset>';
	echo '</form>	';

}

?>