<!DOCTYPE html>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>


<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/new_competition.css'); </style>


<title>Create Course</title>
</head>
<body>
  <h1>New Course Page</h1>

  <div id="new_competition_form">

  <?php

    $course_name = array('name' => 'title', 'id' => 'title');

    echo form_open('admin/createCourse');
    
      //event_type_id of 1 for course
      echo form_hidden('event_type_id','2');
      echo "<h2>Create a new course</h2>";

      echo form_label('Course name:', 'course_name');
      echo form_input($course_name);              
      echo '<br />';

      echo form_submit('Submit', 'Submit');

    echo form_close();       

  echo validation_errors();
  echo '<div id="error">'.$error.'</div>';
  ?>
    
  </div>

</body>
</html>
