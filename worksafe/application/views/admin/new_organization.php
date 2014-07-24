<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/login.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<script>

</script>

<title>Competition</title>
</head>
<body>
<span class="button" id="toggle-login">Login</span>

<div id="login">
  <div id="triangle"></div>
  <h2>New Organization</h2>
<?php
  
  $email = array('name' => 'email', 'placeholder' => 'Email');
  $name = array('name' => 'name', 'placeholder' => 'Name');

  echo form_open('admin/addOrganization/'.$competition->EVENT_ID);
  echo form_label('Email: ', 'email');
  echo form_input($email);
  echo form_label('Name: ', 'name');
  echo form_input($name);
  echo form_submit('admin_new_org', 'Create Organization');
  echo form_close();

  echo validation_errors();
  echo '<div id="success">'.$success.'</div>';
    
  ?>
  <br />
  <a style="text-align:center;" href="<?= base_url();?>index.php/admin/competition">Home page</a>
</div>

</div>


</body>
</html>
