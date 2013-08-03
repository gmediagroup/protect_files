<?php

	header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' ); 
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); 
	header( 'Cache-Control: no-store, no-cache, must-revalidate' ); 
	header( 'Cache-Control: post-check=0, pre-check=0', false ); 
	header( 'Pragma: no-cache' );
	
	
	$step=@$_REQUEST['step'];
	include 'private/config.php';

	switch($step)
	{
		
		case '2':
			$url='http://profit-bill.com/api/get_api_tariffs_1?smsapi_id='.intval($config_smsapi_id).'&secret='.rawurlencode($config_smsapi_secret);
			
			$r=file_get_contents($url);
			$r=trim($r);
			if($r) {
				switch($r)
				{
				
					case 'smsapi_not_found':
					
						$status='<font color=red>Ошибка: SMS API проект не найден. Проверьте параметр config_smsapi_id в файле private/config.php </font>';
					
					break;
					
					case 'secret_is_empty':
						$status='<font color=red>Ошибка: Секретный код не задан в настройках SMS API проекта</font>';
					break;
					
					case 'secret_invalid':
						$status='<font color=red>Ошибка: Секретный код, заданный в файле private/config.php не совпадает с кодом в настройках SMS API проекта</font>';
					break;
					
					case 'smsapi_denied':
						$status='<font color=red>Ошибка: SMS API проект запрещен модератором</font>';
					break;
					
					case 'smsapi_no_nums':
						$status='<font color=red>Ошибка: SMS API проект не имеет подключенных коротких номеров</font>';
					break;
					

				
				}
			}
			else $status.='<br/><font color=red>Ошибка: При обращении к серверу profit-bill произошла ошибка</font>';
			
			if($status) {
			
				$status.='<br/><br/>'.'<form method="post" action="setup.php?step=2"><input type="submit" value="Повторить попыту"/></form>';
			}
			else {
			$status='<font color=green>Скрипт ProtectFiles был успешно установлен. Рекомендуем удалить файл setup.php</font>';
			$f=fopen('private/tariff.txt', 'w+');
			fwrite($f, $r);
			fclose($f);
			}
			
			
			
			
		
		break; 
		
		default:
			$status='';
			 if(!is_writable('keys')) $status.='<font color=red>Папка keys недоступна для записи. Установите права 666 на папку keys</font><br/>';
			 if(!is_writable('private')) $status.='<font color=red>Папка private недоступна для записи. Установите права 666 на папку private</font><br/>';
			 if($config_lifetime<=0) $status.='<font color=red>Срок действия кода доступа должен быть больше нуля (смотрите файл private/config.php)</font><br/>';
			 
			 if(!$config_smsapi_id) $status.='<font color=red>Не указан id SMS API проекта (смотрите файл private/config.php) </font>';
			 if(!$config_smsapi_secret) $status.='<font color=red>Не указан секретный код SMS API проекта (смотрите файл private/config.php) </font>';
			 
			 if(!$status) $status='<form method="get" action="setup.php"><input type=hidden name=step value="2"/>Всё впорядке. <input type="submit" value="Нажмите эту кнопку для продолжения"/></form>';
			 else $status.='<br/>Исправьте приведенные выше ошибки и повторите установку снова. <form method="get" action="setup.php"><input type="submit" value="Повторить проверку"/></form>';
		break;
	}







?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>ProtectFiles by profit-bill.com</title>
	 <link rel="stylesheet" type="text/css" href="helpers/css.css" />
</head>
<body class="oneColLiqCtrHdr">
<div id="container">
  <div id="mainContent">
		<h3>Установка скрипта ProtectFiles. </h3><br/>
		<?php echo $status; ?>
		<?php
		
		
		?><br/><br/>
	</div>
	
</div>
</body>
</html>
	
	