<!DOCTYPE html>
<html>
<head>

<style>@import url('<?=base_url()?>/assets/css/header.css'); </style>
<style>@import url('<?=base_url()?>/assets/css/participant_show_answer.css'); </style>


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

<?php 
echo '<span class="button" id="toggle-login">'.$competition->name.' </span>';
?>
<div id="answer_page">
  <div id="triangle"></div>
  <h2>Worksafe Week Answer</h2>
  <div id = "answer_form">
<?php


  if($correct == TRUE)
  {
    echo '<p id="correct">CORRECT!</p>';
  }
  else
  {
    echo '<p id="wrong">Nice try! The correct answer is....</p>';
  }

    //input the question into the form  
    echo '<p id = "question">'.$question->question.'</p>';


    foreach ($answer as $ans) {

        $radio_input = array('name' => 'answer', 'value' => $ans->answer_id);
        if($ans->correct == 'y')
        {
          echo '<p class = "answer" id = "correct_answer">'.$ans->answer.'</p>';
        }
        else
        {
          echo '<p class = "answer">'.$ans->answer.'</p>';
        }

      
    }
    echo "<br />";

echo '<a href="'.base_url().'index.php/participant/questionPage">Next Question</a>';

?>
</div>
</div>



</body>
</html>
