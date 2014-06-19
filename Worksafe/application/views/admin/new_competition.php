<!DOCTYPE html>
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

<script>
/*
function validateForm() {
    var from = document.forms["createComp"]["from"].value;
    var to = document.forms["createComp"]["to"].value;
    var ans = document.forms["createComp"]["num_answers"].value;
    var question = document.forms["createComp"]["num_questions_per_day"].value;

        //check if from date is null
    if(from==null || from==""){
        document.getElementById("comp_from").innerHTML = "The from date must not be empty!";
        document.getElementById("comp_from").style.color = "red";
        return false;
        }

    else{
        document.getElementById("comp_from").innerHTML = "";
        }

        //check if to date is null
    if(to==null || to=="") {
        document.getElementById("comp_to").innerHTML = "The to date must not be empty!";
        document.getElementById("comp_to").style.color = "red";
        return false;
        }

    else{
        document.getElementById("comp_to").innerHTML = "";
        }


        //validate the # of questions per day isn't blank or less than 1
    if (question==null || question==""){
        document.getElementById("comp_question").innerHTML = "Must not be empty!";
        document.getElementById("comp_question").style.color = "red";
      return false;
        }
    else if(question < 1) {
        document.getElementById("comp_question").innerHTML = "Number of questions per day must be greater than 0";
        document.getElementById("comp_question").style.color = "red";
        return false;
        }

    else{
        document.getElementById("comp_question").innerHTML = "";
        }

        //make sure # of questions per day is an integer
    if(Math.floor(question) == question && $.isNumeric(question))
        {}
    else{
        document.getElementById("comp_question").innerHTML = "Value must be a number";
        document.getElementById("comp_question").style.color = "red";
        return false;
        }

        //validate the # of answers per day isn't blank or less than 2
    if (ans==null || ans=="") {
        document.getElementById("comp_answer").innerHTML = "Must not be empty!";
        document.getElementById("comp_answer").style.color = "red";
        return false;
        }
    else if(ans <= 1) {
        document.getElementById("comp_answer").innerHTML = "Number of answers per question must be greater than 1";
        document.getElementById("comp_answer").style.color = "red";
        return false;
    }

        //make sure # of answers per question is an integer
    if(Math.floor(ans) == ans && $.isNumeric(ans))
        {}

    else{
        document.getElementById("comp_answer").innerHTML = "Value must be a number";
        document.getElementById("comp_answer").style.color = "red";
        return false;
        }
}
*/
</script>

<title>UPLOAD</title>
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
    
      echo "<h2>Create a new competition</h2>";
      echo '<p id="dates">Select dates of competition:</p>';
      echo form_label('From:','from');
      echo form_input($from_input);
      echo '<div id="comp_from"></div>';

      echo form_label('To:', 'to');
      echo form_input($to_input);    
      echo '<div id="comp_to"></div>';             
      echo '<br />';

      echo form_label('Select number of questions per day: ', 'num_questions_per_day');
      echo form_input($question_input);
      echo '<div id="comp_question"></div>';
      echo '<br />';

      echo form_label('Select number of answers per question: ', 'num_answers');
      echo form_input($answer_input);
      echo '<div id="comp_answer"></div>';
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
