function validateForm() {
    var x = document.forms["signup"]["user"].value;
    if (x == null || x == "") {
        return false;
    }
    
    var x = document.forms["signup"]["pass1"].value;
    if (x == null || x == "") {
        return false;
    }
    
    var x = document.forms["signup"]["pass2"].value;
    if (x == null || x == "") {
        return false;
    }
    
    var x = document.forms["signup"]["email"].value;
    if (x == null || x == "") {
        return false;
    }
    
    var x = document.forms["signup"]["name"].value;
    if (x == null || x == "") {
        return false;
    }
    
    var x = document.forms["signup"]["surname"].value;
    if (x == null || x == "") {
        return false;
    }
    
    var x = document.forms["signup"]["name_team"].value;
    if (x == null || x == "") {
        return false;
    }
    
    var x = document.forms["signup"]["telephone"].value;
    if (x == null || x == "") {
        return false;
    }
    
    var x = document.forms["signup"]["privacy"].checked;
    if (x == null || x == "" || x==false) {
        return false;
    }
    
    return true;
};

function buttonSignup(){
    if(validateForm()){
        document.getElementById("reg_button").disabled=false;   
    }else{
        document.getElementById("reg_button").disabled=true;   
    }
};