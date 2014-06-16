<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/participant_question_page.css'); </style>


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

<?php $test = $competition->result();
echo '<span class="button" id="toggle-login">'.$test[0]->name.' </span>';
?>
<div id="question_page">
  <div id="triangle"></div>
  <h2>Worksafe Week Questions</h2>

</div>



</body>
</html>
