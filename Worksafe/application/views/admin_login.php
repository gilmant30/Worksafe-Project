<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/admin_login.css'); </style>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript">

$('#toggle-login').click(function(){
  $('#login').toggle();
});


</script>

<title>Competition</title>
</head>
<body>
<!--<h1>Admin Login Page</h1> -->

<span class="button" id="toggle-login">Login</span>

<div id="login">
  <div id="triangle"></div>
  <h2>Worksafe Week Admin Login</h2>
  <?php
	$input = array('name' => 'username', 'placeholder' => 'Username');
	$password = array('name' => 'password', 'placeholder' => 'Password');

	echo form_open('admin/login');
	echo form_input($input);
	echo form_password($password);
	echo form_submit('admin_login_submit', 'Log in');
  	echo form_close();

  	if($error == TRUE)
  	{
  		echo "<div>Incorrect Username or Password</div>";
  	}
  ?>
</div>

</body>
</html>
