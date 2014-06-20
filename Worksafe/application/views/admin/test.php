<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/login.css'); </style>


<script type="text/javascript" src="<?= base_url();?>assets/js/jquery-1.11.1.min.js" ></script>
<script type="text/javascript" src="<?= base_url();?>assets/js/create_question.js" ></script>


<title>Competition</title>
</head>
<body>
<h1>Admin Login Page</h1> 

<span class="button" id="toggle-login">Login</span>

<div id="login">
  <div id="triangle"></div>
  <h2>WorkSAFE Week Admin Login</h2>
  <?php



  $options = array(
    'multiple_choice' => 'Multiple choice',
    'true_false' => 'True/False',
    'muliple_select' => 'Select multiple'
    );
  $options_id = 'id="options" onChange="create_answer_field();"';
	$question = array('name' => 'question', 'placeholder' => 'Question');
	$category = array('name' => 'category', 'placeholder' => 'Category');
  $date = array('name' => 'question_date', 'placeholder' => 'Date');

 

  echo form_open('admin/testQuestions');
  echo form_input($question);
  echo form_input($category);
  echo form_input($date);
  echo form_dropdown('type', $options, 'multiple_choice', $options_id);
  echo '<br />';

  echo '<div id="add_answer">';
  echo '<br />';
  echo '<input type="hidden" name="num_answers" id="num_answers" value="0"/>';
  echo '<a href="" id="add_multi_choice_answer">Add answer field</a>';
  echo '</div>';

  echo '<br />';
  echo form_submit('admin_create_question', 'Create question');
  echo form_close();


  	
  ?>
</div>

</body>
</html>
