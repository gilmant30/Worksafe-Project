<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/participant_info.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<script>

</script>

<title>Training home page</title>
</head>
<body>
	<br />

<h1>Training home page</h1>
	
<?php
$email = array('name' => 'email', 'placeholder' => 'Email');
$password = array('name' => 'password', 'placeholder' => 'Password');

echo form_open('training/login');
echo form_input($email);
echo form_password($password);
echo form_submit('training_login', 'Log in');
echo form_close();

?>

<div id="error"><?=$error?></div>

<br />

</div>

</body>
</html>