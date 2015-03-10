<?php
include('header.php');
?>


<?php

if($_SERVER['REQUEST_METHOD']=='POST'){

    if(isset($_POST['user']) && isset($_POST['pass'])){
        $user=$_POST['user'];
        $pass=$_POST['pass'];

        $encoded_pass=md5($pass);

        $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
        $user_data=$database->getUserByUsername($user);
        if($user_data!=null){
            if($encoded_pass==$user_data->getPassword()){
                session_regenerate_id();
                $_SESSION['username'] = $user_data->getUsername();
                $_SESSION['auth'] = $user_data->getAuth();
                session_write_close();

                if(isset($_SESSION['old_url'])){
                    header("Location:".$_SESSION['old_url']);
                }else{
                    header("Location:index.php");
                }
            }else{
                error_log("Password Non Corretta $encoded_pass-".$user_data->getPassword()."--normale=".$pass);
            }
        }
    }

}else if(isset($_SESSION['username'])) {
     header("Location:index.php");
}else if($_SERVER['REQUEST_METHOD']=='GET'){ ?>

    <form action="login.php" class="login_form" method="post">
        <h3>Username: </h3><input name="user" type="text">
        <h3>Pass: </h3><input name="pass" type="password">
        <input type="submit" name="Login">
		<a href="signup.php">Non sei ancora registrato? Iscriviti</a>
    </form>

<?php
}

?>

<?php include('footer.php'); ?>