<?php
	include 'private/config.php';
		
	if ($handle = opendir('keys')) {
    while (false !== ($file = readdir($handle))) {
		if($file{0}=='.') continue;
		$ff='keys/'.$file;
		
		if(filemtime($ff)+$config_lifetime*60<time() || file_get_contents($ff)==0) @unlink($ff);

		
		}
    closedir($handle);
	}
	
	$code=@trim(@$_POST['code']);
	
	if(is_file('keys/'.$code)) {
		$c=file_get_contents('keys/'.$code);
		
		$c--;
		$f=fopen('keys/'.$code, 'w+');
		fwrite($f, $c);
		fclose($f);
		
		
		
		include_once 'helpers/ndl.class.php';
		$ndl = new NDL ($_GET["file"], $config_dirname, "download", CD_DISPLAY);
		$ndl->send(0);
		die;
	}
	elseif($code) { $code_error='<font color=red>Вы ввели неверный или просроченный код</font>'; }
	
	
	$file_name=$_GET['file'];
	$payment_info=@file_get_contents('private/tariff.txt');
	
	include 'template.php';
?>