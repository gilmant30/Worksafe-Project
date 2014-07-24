<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/participant_show_answer.css'); </style>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<title>Competition</title>
</head>
<body>
<!--<h1>Admin Login Page</h1> -->

<?php 
echo '<span class="button" style="width:350px;">'.$category.' </span>';
?>
<div id="answer_page">
  <div id="triangle"></div>
  <h2>Worksafe Week Answer</h2>
  <div id = "answer_form">
<?php

    //input the question into the form  
    echo '<p id = "question">'.$question->QUESTION->load().'</p>';

    if($answer_type == 'true_false')
    {
        if($correct == TRUE)
        {
          echo '<p id="correct">CORRECT! you selected <strong>'.$answer.'</strong></p>';
        }
        else
        {
          echo '<p id="wrong">Nice try!....the correct answer is <strong>'.$answer.'</strong></p>';
        }
    }

    else if($answer_type == 'multiple_choice')
    {
      if($correct == FALSE)
      {
        echo '<p id="incorrect_multi">Nice try!....the correct answer is...</p>';
      }
    foreach ($answer as $ans) {

        //$radio_input = array('name' => 'answer', 'value' => $ans->ANSWER_ID);
        if($ans->CORRECT == 'y')
        {
          if($correct == TRUE)
          {
            echo '<p>CORRECT!</p>';
          }
          echo '<p class="answer" id = "correct_answer">'.$ans->ANSWER->load().'</p>';
        }
      
      }
    }

    else if($answer_type == 'multiple_select')
    {
      if($correct == FALSE)
      {
        echo '<p id="incorrect_multi">Nice try!....the correct answer is...</p>';
      }
    foreach ($answer as $ans) {

        //$radio_input = array('name' => 'answer', 'value' => $ans->ANSWER_ID);
        if($ans->CORRECT == 'y')
        {
          if($correct == TRUE)
          {
            echo '<p>CORRECT!</p>';
          }
          echo '<p class="answer" id = "correct_answer">'.$ans->ANSWER->load().'</p>';
        }
      
      }
    }
    echo "<br />";

echo '<a href="'.base_url().'index.php/competition/questionPage">Next Question</a>';

?>
</div>
</div>



</body>
</html>
