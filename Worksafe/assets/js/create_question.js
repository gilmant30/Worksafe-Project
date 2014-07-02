var a = 0;


//add input fields when add answer box is pressed with muliple choice answers
$(function(){
  $(document).on('click', '#add_multi_choice_answer', function() {
   $('<input type="text" id="answer" size="40" name="answer'+a+'" value="" placeholder="Answer" required/><input type="radio" id="correct_ans" name="radio_answer'+a+'" value="correct_ans'+a+'"/><label>Correct answer</label>').appendTo($('#add_answer'));
  
   $('#num_answers').val(a);
   a++;
  return false;
  });
});

//add input fields when add answer box is pressed with multi select answers
$(function(){
  $(document).on('click', '#add_multi_select_answer', function() {
   $('<input type="text" id="answer" size="40" name="answer'+a+'" value="" placeholder="Answer" required/><input type="checkbox" id="correct_ans" name="checkbox_answer'+a+'" value="correct_ans'+a+'"/><label>Correct answer</label>').appendTo($('#add_answer'));
  
   $('#num_answers').val(a);
   a++;
  return false;
  });
});

function displayTrueFalse(){
  $('div#add_answer').replaceWith('<div id="add_answer"><input type="radio" name="answer" value="true"/><label>True</label><br /><input type="radio" name="answer" value="false"/><label>False</label></div>');
}

function displayMultipleSelect(){
  $('div#add_answer').replaceWith('<div id="add_answer"><input type="hidden" name="num_answers" id="num_answers" value="0"/><br /><a href="" id="add_multi_select_answer">Add answer field</a><br /></div>');
  a = 0;
}

function displayMultipleChoice(){
  $('div#add_answer').replaceWith('<div id="add_answer"><input type="hidden" name="num_answers" id="num_answers" value="0"/><br /><a href="" id="add_multi_choice_answer">Add answer field</a><br /></div>');
  a = 0;
}

function create_answer_field(){
  var value = String($('#options').val());
  if(value == 'true_false')
  {
   displayTrueFalse();
  }
  else if(value == 'multiple_select')
  {
    displayMultipleSelect();
  }
  else if(value == 'multiple_choice')
  {
    displayMultipleChoice();
  }
}






