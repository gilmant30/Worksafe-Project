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
<?php

  //set q variable to 0
  $q=0;

  $label_attributes = array(
    'id' => 'answer_label'
    );

  echo form_open('participant/answerQuestions');
  foreach ($question as $quest) {
    //set a variable to 0


    //input the question into the form  
    echo '<p id = "question">'.$quest[0]->question.'</p>';



    foreach ($answer as $ans) {

      if($ans->question_id == $quest[0]->question_id)
      {
        $radio_input = array('name' => 'correct_ans_q'.$q, 'value' => $ans->answer_id);
        echo form_radio($radio_input);
        echo form_label($ans->answer,'question', $label_attributes);
      echo "<br />";
      }
      
    }
    echo "<br />";
    $q++;

  }

  echo form_submit('particpant_answer_question_submit', 'Submit Answers');
  echo form_close();
?>
</div>



</body>
</html>
