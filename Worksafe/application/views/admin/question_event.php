<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
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
<h1>Admin Select Event Page</h1> 

<span class="button" id="toggle-login">Event</span>

<div id="login">
  <div id="triangle"></div>
  <h2>Select the event for the question:</h2>
  <?php

  
  echo form_open('admin/createQuestion');

  echo '<select id="options" name="event_id">';
  foreach($events->result() as $type)
  {
    echo '<option value ="'.$type->EVENT_ID.'">'.$type->EVENT_NAME.'</option>';
  }
  echo '</select>';

  echo '<br />';
  echo form_submit('admin_create_question', 'Create question');
  echo form_close();

  echo '<div>'.$error.'</div>';
  echo '<br />';
  echo '<a href="'.base_url().'index.php/admin/competition">Home page</a>';
  	
  ?>
</div>

</body>
</html>
