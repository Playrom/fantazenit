<?php
include('header.php');
?>


<?php

if(isset($_SESSION['username'])) {
     header("Location:home.php");

}else if(isset($_POST['user']) && isset($_POST['pass1']) && isset($_POST['pass2']) && isset($_POST['email']) && isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['name_team']) &&      isset($_POST['telephone'])){
    
    
    $pass1=$_POST['pass1'];
    $pass2=$_POST['pass2'];
    $pass="";
    
    $username=$_POST['user'];
    $email=$_POST['email'];
    $name=$_POST['name'];
    $surname=$_POST['surname'];
    $name_team=$_POST['name_team'];
    $telephone=$_POST['telephone'];

    $_SESSION['user_temp']=$username;
    $_SESSION['email_temp']=$email;
    $_SESSION['name_temp']=$name;
    $_SESSION['surname_temp']=$surname;
    $_SESSION['name_team_temp']=$name_team;
    $_SESSION['telephone_temp']=$telephone;
    
    
    if($pass1==$pass2){
        $pass=md5($pass1);
        
        
        $balance=250;
        if(isset($config['balance'])) $balance=$config['balance'];
        
        $url_fb="NULL";
        if(isset($_POST['url_fb'])) $url_fb=$_POST['url_fb'];
        
        $byEmail = null;
        $byUsername = null;
        
        /*$json = $apiAccess->accessApi("/users/$username","GET");
        
        if($json["error"]==false){
	        $byUsername = $json["data"];
        }
        
        $json = $apiAccess->accessApi("/users/$email","GET");
        
        if($json["error"]==false){
	        $byEmail = $json["data"];
        }*/
        	        
        $arr_data = array(
	        "username" => $username , 
	        "name" => $name , 
	        "surname" => $surname , 
	        "password" => $pass , 
	        "email" => $email , 
	        "balance" => $balance ,
	        "name_team" => $name_team , 
	        "telephone" => $telephone , 
	        "url_fb" => $url_fb
	    );
                 
		$params = array('postParams' => $arr_data);
        
        $json=$apiAccess->accessApi("/users","POST",$params);
                
        $error_code = null;
                
        if($json["error"]==true){
            $error_code = $json["error_code"];
            $_SESSION['wrong_username']=true;
			header("Location:signup.php");
        }else{
            session_destroy();
            header("Location:login.php?reg_complete");
        }
        
        //$user=$database_users->signupUser(new User(-1,$username,$name,$surname,$pass,$email,NULL,0,$balance,NULL,NULL,$name_team,$telephone,$url_fb));
           
        
    }else{
        $_SESSION['wrong_pass']=true;
        header("Location:signup.php");
    }

    
}else{ ?>

    <?php if(isset($_SESSION['wrong_pass'])){ 
		$error_messages[] = "Errore: Le due password non coincidono";    
	} ?>
    
    <?php if(isset($_SESSION['wrong_username'])){ 
		$error_messages[] = "Errore: Username o Email già utilizzati";    
     } ?>
     
    <?php
	     include('error-box.php');
	?>

    <form action="signup.php" name="signup" class="signup_form form-horizontal" method="post">
        <h3>Registrazione al Fanta Zenit</h3>
        <div class="form-group">
            <label class="col-md-2 control-label">Username</label>
            <div class="col-md-10">
              <input type="text" class="form-control" id="user" name="user" placeholder="Username" <?php if(isset($_SESSION['user_temp']) &&!isset($_SESSION['wrong_username'])) echo "value=\"".$_SESSION['user_temp']."\""; ?>>
            </div>
        </div>
    
        <div class="form-group">
            <label class="col-md-2 control-label">Password</label>
            <div class="col-md-10">
              <input type="password" class="form-control" id="pass1" name="pass1" placeholder="Password">
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-2 control-label">Conferma Password</label>
            <div class="col-md-10">
              <input type="password" class="form-control" id="pass2" name="pass2" placeholder="Conferma Password" >
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Email</label>
            <div class="col-md-10">
              <input type="text" class="form-control" id="email" name="email" placeholder="Email"  <?php if(isset($_SESSION['email_temp'])  &&!isset($_SESSION['wrong_username'])) echo "value=\"".$_SESSION['email_temp']."\""; ?>>
            </div>
        </div>
        
        <hr>
        
        <div class="form-group">
            <label class="col-md-2 control-label">Nome</label>
            <div class="col-md-10">
              <input type="text" class="form-control" id="name" name="name" placeholder="Nome" <?php if(isset($_SESSION['name_temp'])) echo "value=\"".$_SESSION['name_temp']."\""; ?> >
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Cognome</label>
            <div class="col-md-10">
              <input type="text" class="form-control" id="surname" name="surname" placeholder="Cognome" <?php if(isset($_SESSION['surname_temp'])) echo "value=\"".$_SESSION['surname_temp']."\""; ?> >
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Nome Squadra</label>
            <div class="col-md-10">
              <input type="text" class="form-control" id="name_team" name="name_team" placeholder="Nome Squadra" <?php if(isset($_SESSION['name_team_temp'])) echo "value=\"".$_SESSION['name_team_temp']."\""; ?> >
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Telefono</label>
            <div class="col-md-10">
              <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Telefono"<?php if(isset($_SESSION['telephone_temp'])) echo "value=\"".$_SESSION['telephone_temp']."\""; ?> >
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-4 control-label">Url Profilo Facebook<small> Campo Non Richiesto</small></label>
            <div class="col-md-8">
              <input type="text" class="form-control" id="url_fb" name="url_fb" placeholder="Url" >
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6">
                <a  class="col-md-12" href="login.php">Sei già registrato? Effettua il Login</a>
            </div>

            <div class="col-md-6">
                <button type="submit" disabled="true" id="reg_button" class="btn btn-default col-md-12">Registrati</button>
            </div>
        </div>
        
        
    </form>

<?php
      session_destroy();
}

?>

<script>

    $("body").on('change', 'input', buttonSignup);
</script>

<?php include('footer.php'); ?>