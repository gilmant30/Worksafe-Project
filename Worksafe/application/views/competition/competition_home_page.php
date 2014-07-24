<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<html>
<head>
<style>@import url('<?=base_url()?>/assets/css/participant_info.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<title>Home Page</title>
</head>
<body>


<div id="body">
<br />
<br />
<br />
<div id="commitment"><?=$commitment?></div>
<h1>Welcome to the 2014 WorkSAFE Week Team Competition!</h1>
</br>
<p>This competition will help engage MEM employees, and raise your safety awareness.</p>
<p>Click the &quot;Learn More&quot; button at the top of the screen for details.</P>
<p>To go to today&#39s questions, please click &quot;START&quot;.</p>

<br />
<?php
if($signup != TRUE)
{
	echo '<a id="start_button" class="button" href="'.base_url().'index.php/competition/questionPage">Start</a>';
}
?>
</div>
</body>
</html>