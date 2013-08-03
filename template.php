<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Скачивание файла</title>
	 <link rel="stylesheet" type="text/css" href="helpers/css.css" />
</head>
<div id="container">
  <div id="mainContent">
    <h3>Вы скачиваете файл: <?php echo $file_name; ?></h3>

	Для продолжения, введите код в форму ниже:
	<form method="POST">
		<input type="text" name="code"/><br/>
		<input type="submit" name="do" value="Скачать"/>
	<?php echo $code_error; ?>
	</form>
	
	
	<br/>
	Если у Вас нет кода доступа, вам необходимо оплатить услугу:
	<br/><br/>
	<?php echo $payment_info; ?>
	
	
	</div>
	
</div>
</html>
	
	