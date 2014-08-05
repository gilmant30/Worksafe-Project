<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<style>@import url('<?=base_url()?>/assets/css/login.css'); </style>

<div id="login">

  <h2>Login</h2>
  <?php
	$email = array('name' => 'email', 'placeholder' => 'Email');
  $label = array('id' => 'email_label');

	echo form_open('competition/login');
  echo form_hidden('competition_id',$competition_id);
  echo form_label('Email:', 'email', $label);
	echo form_input($email);
  echo '<br />';
	echo '<a href="'.base_url().'index.php/competition/signup">Signup</a>';
  echo '<br />';
  echo '<br />';
	echo form_submit('participant_login_submit', 'Log in');
  echo form_close();
  
  echo validation_errors();
  echo '<div id="error">'.$error.'</div>';
  ?>
</div>


