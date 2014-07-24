<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/participant_question_page.css'); </style>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>


<title>Question</title>
</head>
<body>
<!--<h1>Admin Login Page</h1> -->

<?php 

echo '<span class="button">'.$category.' </span>';
?>
<div id="question_page">
  <div id="triangle"></div>
  
<?php
   $label_attributes = array(
    'id' => 'answer_label'
    );

   echo '<p>'.$error.'</p>';

//if the question is true or false print out this form   
if($question->QUESTION_TYPE == 'true_false')
{
  echo "<h2>True or False</h2>";
  echo form_open('competition/answerTrueFalseQuestion');
  //input the question into the form  
  echo '<p id = "question">'.$question->QUESTION->load().'</p>';
  echo '<br />';

  $radio_input_true = array('name' => 'answer', 'value' => 'true');
  $radio_input_false = array('name' => 'answer', 'value' => 'false');

  echo form_hidden('answer_id', $answer[0]->ANSWER_ID);
  echo form_radio($radio_input_true);
  echo form_label('True','question', $label_attributes);
  echo '<br />';
  echo form_radio($radio_input_false);
  echo form_label('False','question', $label_attributes);
  echo '<br />';
  if($question->SOURCE_LINK != NULL)
  {
    echo '<p><a href ="'.$question->SOURCE_LINK.'">Need a hint?</a></p>';
  }
  echo '<br />';
  echo form_submit('particpant_answer_question_submit', 'Submit Answer');
  echo form_close();
  
  
}

//if the question is multiple choice print out this form
else if($question->QUESTION_TYPE == 'multiple_choice')
{
  echo "<h2>Multiple Choice</h2>";
  echo form_open('competition/answerMultipleChoiceQuestion');

    //input the question into the form  
    echo '<p id = "question">'.$question->QUESTION->load().'</p>';
    echo '<br />';

    foreach ($answer as $ans) {
        $radio_input = array('name' => 'answer', 'value' => $ans->ANSWER_ID);
        echo form_radio($radio_input);
        echo form_label($ans->ANSWER->load(),'question', $label_attributes);
      echo "<br />";
      
    }
    if($question->SOURCE_LINK != NULL)
    {
      echo '<p><a href ="'.$question->SOURCE_LINK.'">Need a hint?</a></p>';
    }
    echo "<br />";


  echo form_submit('particpant_answer_question_submit', 'Submit Answer');
  echo form_close();
}


//if the question is multiple select print out this form
else if($question->QUESTION_TYPE == 'multiple_select')
{
  echo "<h2>Multiple Select</h2>";
  echo form_open('competition/answerMultipleSelectQuestion');
  echo '<p id="title">Select All That Apply</p>';  

    //input the question into the form  
    echo '<p id = "question">'.$question->QUESTION->load().'</p>';
    echo '<br />';
    $a=0;
    foreach ($answer as $ans) {
        $checkbox_input = array('name' => 'answer[]', 'value' => $ans->ANSWER_ID);
        echo form_checkbox($checkbox_input);
        echo form_label($ans->ANSWER->load(),'question', $label_attributes);
      echo "<br />";
      $a++;
    }

    echo form_hidden('num_answers', $a);
    echo "<br />";
    if($question->SOURCE_LINK != NULL)
    {
      echo '<p><a href ="'.$question->SOURCE_LINK.'">Need a hint?</a></p>';
    }   
    echo "<br />";


  echo form_submit('competition_answer_question_submit', 'Submit Answers');
  echo form_close();
}
?>
</div>



</body>
</html>
