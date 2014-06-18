<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<script>

</script>

<title>Competition</title>
</head>
<body>
<h1>Competition Review Page</h1>
<?php
foreach ($review as $question) {
	echo '<p>Question: '.$question['question_name'].'</p>';
	echo '<p>Date: '.$question['question_date'].'</p>';

	foreach ($question['answer_data'] as $value) {
		if($value->correct == 'y')
		{
			echo '<p style = "color:green;"">'.$value->answer.'</p>';
		}
		else
		{
			echo '<p>'.$value->answer.'</p>';
		}
	}

	echo "<br />";
}

?>

<a href="<?= base_url();?>index.php/admin/Competition">Competition home page</a>

</body>
</html>