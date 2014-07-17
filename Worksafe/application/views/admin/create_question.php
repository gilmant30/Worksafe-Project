<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/new_competition2.css'); </style>


<script type="text/javascript" src="<?= base_url();?>assets/js/jquery-1.11.1.min.js" ></script>
<script type="text/javascript" src="<?= base_url();?>assets/js/create_question.js" ></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

 <script>
  $(function() {
    $( "#question_date" ).datepicker();
  });
  </script>

<title>Competition</title>
</head>
<body>
<h1>Admin Create Question Page</h1> 

<span class="button" id="toggle-login">Create</span>

<div id="login">
  <div id="triangle"></div>
  <h2>Create Question</h2>
  <?php

   echo '<div id="added">'.$added.'</div>';

  $options = array(
    'multiple_choice' => 'Multiple choice',
    'true_false' => 'True/False',
    'multiple_select' => 'Select multiple'
    );
  $options_id = 'id="options" onChange="create_answer_field();"';
	$question = array('name' => 'question', 'placeholder' => 'Question');
	$category = array('name' => 'category', 'placeholder' => 'Category');
  $date = array('name' => 'question_date', 'id' => 'question_date', 'placeholder' => 'Date');
  $source = array('name' => 'source', 'id' => 'source', 'placeholder' => 'Source link');


  echo form_open('admin/uploadQuestion');
  echo form_hidden('event_id', $event->EVENT_ID);
  echo form_label('Question: ', 'question');
  echo form_input($question);
  echo form_label('Category: ', 'category');
  echo form_input($category);
  if($event->EVENT_TYPE_ID == '1')
  {
    echo form_label('Date: ', 'question_date');
    echo form_input($date);
  }
  echo form_label('Source link: ', 'source');
  echo form_input($source);
  echo form_label('Question type: ', 'option_type');
  echo form_dropdown('option_type', $options, 'multiple_choice', $options_id);
  echo '<br />';

  echo '<div id="add_answer">';
  echo '<br />';
  echo '<input type="hidden" name="num_answers" id="num_answers" value="0"/>';
  echo '<a href="" id="add_multi_choice_answer">Add answer field</a>';
  echo '</div>';

  echo '<br />';
  echo form_submit('admin_create_question', 'Create question');
  echo form_close();

  echo '<div id="error">'.$error.'</div>';
  	
  ?>
</div>

</body>
</html>
