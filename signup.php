<?php
include('header.php');
?>


<?php

if(isset($_SESSION['username'])) {
     header("Location:index.php");

}else if(isset($_POST['user']) && isset($_POST['pass1']) && isset($_POST['pass2']) && isset($_POST['email']) && isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['name_team']) &&      isset($_POST['telephone'])){
    
    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $config=$database->dumpConfig();
    
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
        
        if($database->getUserByEmail($email)==null && $database->getUserByUsername($username)==null){
            $user=$database->signupUser(new User(-1,$username,$name,$surname,$pass,$email,NULL,0,$balance,NULL,NULL,$name_team,$telephone,$url_fb));
            if($user) {
                session_destroy();
                header("Location:index.php");
            }
        }else{
            $_SESSION['wrong_username']=true;
            header("Location:signup.php");
        }
        
    }else{
        $_SESSION['wrong_pass']=true;
        header("Location:signup.php");
    }

    
}else{ ?>

    <?php if(isset($_SESSION['wrong_pass'])){ ?>
        <div class="error_display esecution_error">Errore: Le due password non coincidono</div>
    <?php } ?>
    
    <?php if(isset($_SESSION['wrong_username'])){ ?>
        <div class="error_display esecution_error">Errore: Username o Email già utilizzati</div>
    <?php } ?>

    <form action="signup.php" name="signup" class="login_form signup_form" method="post">
        <h3>Registrazione al Fanta Zenit</h3>
        <div class="setting_item">
            <div class="setting_item_name">Username</div>
            <input class="setting_item_input" size="30" type="text" name="user" <?php if(isset($_SESSION['user_temp']) &&!isset($_SESSION['wrong_username'])) echo "value=\"".$_SESSION['user_temp']."\""; ?> >
        </div>
    
        <div class="setting_item">
            <div class="setting_item_name">Password</div>
            <input class="setting_item_input" size="30" type="password" name="pass1" >
        </div>
        
        <div class="setting_item">
            <div class="setting_item_name">Ripeti Password</div>
            <input class="setting_item_input" size="30" type="password" name="pass2" >
        </div>
        
        <div class="setting_item">
            <div class="setting_item_name">Email</div>
            <input class="setting_item_input" size="30" type="text" name="email" <?php if(isset($_SESSION['email_temp'])  &&!isset($_SESSION['wrong_username'])) echo "value=\"".$_SESSION['email_temp']."\""; ?> >
        </div>
        
        <hr>
        
        <div class="setting_item">
            <div class="setting_item_name">Nome</div>
            <input class="setting_item_input" size="30" type="text" name="name" <?php if(isset($_SESSION['name_temp'])) echo "value=\"".$_SESSION['name_temp']."\""; ?> >
        </div>
        
        <div class="setting_item">
            <div class="setting_item_name">Cognome</div>
            <input class="setting_item_input" size="30" type="text" name="surname" <?php if(isset($_SESSION['surname_temp'])) echo "value=\"".$_SESSION['surname_temp']."\""; ?> >
        </div>
        
        <div class="setting_item">
            <div class="setting_item_name">Nome Squadra</div>
            <input class="setting_item_input" size="30" type="text" name="name_team" <?php if(isset($_SESSION['name_team_temp'])) echo "value=\"".$_SESSION['name_team_temp']."\""; ?> >
        </div>
        
        <div class="setting_item">
            <div class="setting_item_name">Telefono</div>
            <input class="setting_item_input" size="30" type="text" name="telephone" <?php if(isset($_SESSION['telephone_temp'])) echo "value=\"".$_SESSION['telephone_temp']."\""; ?> >
        </div>
        
        <div class="setting_item">
            <div class="setting_item_name">Url Profilo Facebook</div>
            <div class="setting_item_descript">Campo non richiesto</div>
            <input class="setting_item_input" size="30" type="text" name="url_fb" >
        </div>
        
		<a class="half_size" href="login.php">Sei già registrato? Effettua il Login</a>
        <input disabled="true" class="half_size" id="reg_button" type="submit" name="Registrati" value="Registrati">
        
    </form>

<?php
      session_destroy();
}

?>

<script>

    $("body").on('change', 'input.setting_item_input', buttonSignup);
</script>

<?php include('footer.php'); ?>