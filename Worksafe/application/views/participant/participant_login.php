<!DOCTYPE html>
<html>
<head>


<style>@import url('<?=base_url()?>/assets/css/login.css'); </style>


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
  <h2>WorkSAFE Week Login</h2>
  <?php
	$email = array('name' => 'email', 'placeholder' => 'Email');

	echo form_open('participant/login');
	echo form_input($email);
	echo '<a href="'.base_url().'index.php/participant/signup">Signup</a>';
  echo '<br />';
  echo '<br />';
	echo form_submit('participant_login_submit', 'Log in');
  echo form_close();
  
  echo validation_errors();
  echo '<div id="error">'.$error.'</div>';
  ?>
</div>

</body>
</html>
