<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
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
  <h2>WorkSAFE Week Signup</h2>
  <?php
	$email = array('name' => 'email', 'placeholder' => 'Email');
	$zip = array('name' => 'zipcode', 'placeholder' => 'Zipcode');
  
	echo form_open('competition/enroll');
	echo form_input($email);
	//echo form_input($zip);
  echo form_label('Team: ','org_id');
  echo '<select id="org_id" name="organization">';
  foreach ($organization->result() as $row) {
    echo "<option value=$row->USER_ID>$row->USER_NAME</option>";
  }
  echo '</select>';

  echo '<a href="'.base_url().'index.php/competition">Already a user?</a>';
  echo '<br />';
  echo '<br />';
	echo form_submit('particpant_signup_submit', 'Register');
  echo form_close();

  echo validation_errors();
  echo '<div id="error">'.$error.'</div>';
  ?>
</div>



</body>
</html>
