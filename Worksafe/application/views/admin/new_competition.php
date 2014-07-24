<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>


<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/new_competition.css'); </style>

<script>
  $(function() {
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      minDate: 0,
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
</script>


<title>Create Compeition</title>
</head>
<body>
  <h1>New Competition Page</h1>

  <div id="new_competition_form">

  <?php

    $attributes = array('name' => 'createComp', 'onsubmit' => 'return validateForm()');
    $from_input = array('name' => 'from', 'id' => 'from');
    $to_input = array('name' => 'to', 'id' => 'to');
    $question_input = array('name' => 'num_questions_per_day', 'id' => 'question');
    $answer_input = array('name' => 'num_answers', 'id' => 'answer');
    $title = array('name' => 'title', 'id' => 'title');

    echo form_open('admin/createCompetition', $attributes);
    
      //event_type_id of 1 for competition
      echo form_hidden('event_type_id','1');
      echo "<h2>Create a new competition</h2>";
      echo '<p id="dates">Select dates of competition:</p>';
      echo form_label('From:','from');
      echo form_input($from_input);
      echo '<div id="comp_from"></div>';

      echo form_label('To:', 'to');
      echo form_input($to_input);    
      echo '<div id="comp_to"></div>';             
      echo '<br />';
      
      echo form_label('Name of competition: ', 'title');
      echo form_input($title);
      echo '<br />';

      echo form_submit('Submit', 'Submit');

    echo form_close();       

  echo validation_errors();
  echo '<div id="error">'.$error.'</div>';
  echo '<div id="error">'.$error_title.'</div>';
  ?>
    
  </div>

</body>
</html>
