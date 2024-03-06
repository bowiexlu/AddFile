<?php
	# h�mtar filnamnet fr�n webbl�sarens adressf�lt
	$filename = $_GET['file'];
	# mappens namn d�r filen sparats
	$upload_dir = 'files/';
	# s�kv�g till mappen d�r downloadfilen finns
	$filepath = $upload_dir.$filename;
	
	# En vektor(array) med filtyper som ska kunna laddas ned
	# Fler �n vad som kan laddas upp.
	$mime_types = array (
		#arkivfiler
		'zip' => 'application/zip',
		'tar' => 'application/x-tar',
		'exe' => 'application/x-msdownload',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',
		# dokument
		'txt' => 'text/plain',
		'pdf' => 'application/pdf',
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'wmf' => 'application/x-msmetafile',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pub' => 'application/x-mspublisher',
		'ai' => 'application/postscript',
		'ps' => 'application/postscript',
		'css' => 'text/css',
		'htm' => 'text/html',
		'html' => 'text/html',
		'js' => 'application/x-javascript',
		'swf' => 'application/x-shockwave-flash',
		# programfiler
		'exe' => 'application/octet-stream',
		# bildfiler
		'gif' => 'image/gif',
		'png' => 'image/png',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff',
		'eps' => 'application/postscript',
		'ico' => 'image/x-icon',
		'svg' => 'image/svg+xml',
		# fonter
		'ttf' => 'application/x-truetype-font', 
		# ljud
		'mp3' => 'audio/mpeg',
		'wav' => 'audio/x-wav',
		'au' => 'audio/basic',
		'mid' => 'audio/mid',
		# video
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'mov' => 'video/quicktime',
		'qt' => 'video/quicktime',
		'avi' => 'video/x-msvideo',
		'asx' => 'video/x-ms-asf',
	);
	
	
	# H�mtar fil�ndelse fr�n filnamnet och omvandlar till sm� bokst�ver
	$file_extension = strtolower(substr(strrchr($filename,"."), 1));
	# h�mtar mime och filtyp
	$mimetype = $mime_types[$file_extension];
	# kontrollerar om den angivna filen finns och om den �r l�sbar
	if(file_exists($filepath) && is_readable($filepath)){
		#kontrollerar filens storlek
		$filstorlek = filesize($filepath);
		#skickar headers
		header('Content-type: '.$mimetype);
		header('Content-Length: '.$filstorlek);
		header('Content-Disposition : attachment; filename= '.$filename);
		#�ppnar filen med l�sr�ttigheter och skickar headers om allt ok
		$file = fopen($filepath,'r');
		if($file){
			#h�mtar all filinformation
			fpassthru($file);
			#st�nger filen
			fclose($file);
		}
		else{
			echo 'Det g�r inte att h�mta eller l�sa filen.';
		}
	}else{
		echo 'Den valda filen <b>'.$filename.'</b> finns inte eller �r inte l�sbar.';
	}
	
	# sparar information i loggfil; ip-numret, datum, tid + namn
	$ip = $_SERVER['REMOTE_ADDR'];
	$datum = date('d.M.Y');
	$time_local = date('H:i:s');
	$loggfil = fopen($upload_dir.'../download_log.txt', 'a+');
	if ($loggfil) {
		fwrite($loggfil, "Filnamn: ".$filename." | Datum: ".$datum ." | Tid: ".$time_local." | IP-nummer: ".$ip."\r\n");	
		fclose($loggfil);
	}
	
	exit();
?>


















