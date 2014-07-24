<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/login.css'); </style>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>


<title>Competition</title>
</head>
<body>
<h1>Admin Login Page</h1> 

<span class="button" id="toggle-login">Login</span>

<div id="login">
  <div id="triangle"></div>
  <h2>WorkSAFE Week Admin Login</h2>
  <?php
  
	$input = array('name' => 'email', 'placeholder' => 'Email');
	$password = array('name' => 'password', 'placeholder' => 'Password');

	echo form_open('admin/login');
  echo form_label('Email: ', 'email');
	echo form_input($input);
  echo form_label('Password: ', 'password');
	echo form_password($password);
	echo form_submit('admin_login_submit', 'Log in');
  echo form_close();

  echo validation_errors();
  echo '<div id="error">'.$error.'</div>';
  	
  ?>
</div>

</body>
</html>
