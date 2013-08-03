<?php
	include 'private/config.php';
	
	
	
	$key=$_GET['key'];
	if($key!=md5($config_smsapi_secret.$_GET['id'])) do_reply('hacking attempt'); // скрипт был вызван с неправильным параметром безопасности.
	
	
	
	do
	{
		$p1=md5(mt_rand(0,10).mt_rand(0,10).mt_rand(0,10).mt_rand(0,10));
		$value=substr($p1,0,7);
		$fname='keys/'.$value;
	}while(is_file($fname));
	
	$f=fopen($fname, 'w+');
	if($config_lifecount) { 
		fwrite($f, $config_lifecount);
	}
	else fwrite($f, '-1');
	
	fclose($f);
	
	do_reply('Vash kod: '.$value);
	
	
	
	function do_reply($r)
	{
	
	
		echo("ok\n");
		echo($r);
		die;
	}


?>