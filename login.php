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

    <?php if(isset($_GET['reg_complete'])) { ?>
        <div class="alert alert-success error_display" role="alert">
            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>Registrazione Completata!
        </div>
    <?php } ?>

    <form action="login.php" class="login_form form-horizontal" method="post">
        <div class="form-group">
            <label class="col-md-2 control-label">Username</label>
            <div class="col-md-10">
              <input type="text" class="form-control" id="user" name="user" placeholder="Username" >
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Password</label>
            <div class="col-md-10">
              <input type="password" class="form-control" id="pass" name="pass" placeholder="Password">
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6">
                <a  class="col-md-12" href="signup.php">Non sei ancora registrato? Iscriviti</a>
            </div>

            <div class="col-md-6">
                <button type="submit"  class="btn btn-default col-md-12">Login</button>
            </div>
        </div>
    </form>

<?php
}

?>

<?php include('footer.php'); ?>