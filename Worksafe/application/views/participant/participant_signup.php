<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
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
  <h2>Worksafe Week Signup</h2>
  <?php
	$email = array('name' => 'email', 'placeholder' => 'Email');
	$zip = array('name' => 'zipcode', 'placeholder' => 'Zipcode');
  
	echo form_open('participant/enroll');
	echo form_input($email);
	echo form_input($zip);
  echo form_label('Organization: ','org_id');
  echo '<select id="org_id" name="organization">';
  foreach ($organization->result() as $row) {
    echo "<option value=$row->user_id>$row->name</option>";
  }
  echo '</select>';
  //echo form_dropdown('organization',$organization, '',$org);
	echo form_submit('particpant_signup_submit', 'Log in');
  echo form_close();

  	if($error == TRUE)
  	{
  		echo "<div>Incorrect</div>";
  	}
  ?>
</div>



</body>
</html>
