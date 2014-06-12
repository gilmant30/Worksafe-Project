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