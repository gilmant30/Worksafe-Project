<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/review_competition.css'); </style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

<script>

</script>

<title>Competition</title>
</head>
<body>
<h1>Competition Review Page</h1>
<div id = "review_competition">
<div id="update"><?=$update?></div>
<div id="error"><?=$error?></div>
<h2>Questions and answers for competition</h2>

<?php
echo form_open('admin/editCompetition');
foreach ($review as $question) {
	echo '<p>Question: <a href="'.base_url().'index.php/admin/deleteQuestionAssoc/'.$question['question_id'].'">Delete Question</a></p>';
	$array = array('name' => 'q'.$question['question_id'], 'value' => $question['question_name'] );
	echo form_input($array);
	echo '<p id="date">Date: '.$question['question_date'].'</p>';
	echo '<p id="type">Type of question: '.$question['question_type'].'</p>';
	echo '<p>Answers:</p>';
	foreach ($question['answer_data'] as $value) {
		if($value->CORRECT == 'y')
		{
			$answer = array('name' => 'a'.$value->ANSWER_ID, 'value' => $value->ANSWER->load(), 'id' => 'correct');
		}
		else
		{
			$answer = array('name' => 'a'.$value->ANSWER_ID, 'value' => $value->ANSWER->load());
		}
		echo form_input($answer);
	}
		echo "<br />";
		echo "<br />";
}
	echo form_submit('edit_competition', 'Update Competition');
echo form_close();
?>
<br />
<a id="home_link" href="<?= base_url();?>index.php/admin/Competition">Competition home page</a>

</div>

</body>
</html>