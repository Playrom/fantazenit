<?php
include('header.php');
?>

<?php

if($_SERVER['REQUEST_METHOD']=='POST'){

    if(isset($_POST['user']) && isset($_POST['pass'])){

        $user=$_POST['user'];
        $pass=$_POST['pass'];

        $encoded_pass=md5($pass);

        $data = array();

        $data['username']=$user;
        $data['password']=$encoded_pass;

        /*$data= json_encode($data);

        $ch = curl_init('http://associazionezenit.it/fantazenit/api/v1/login');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $json = curl_exec($ch);*/

        $result=$apiAccess->requestToken($user,$encoded_pass);

        if($result['error']==true){
            error_log("Password Non Corretta");
        }else {

            if (isset($result['apiKey'])) {
                $token = $result['apiKey'];
                $_SESSION['userToken'] = $token;
            }


            session_regenerate_id();
            $_SESSION['username'] = $result['data']['username'];
            $_SESSION['userAuth'] = $result['data']['auth'];
            $_SESSION['userId'] = $result['data']['id'];
            session_write_close();

            if (isset($_SESSION['old_url'])) {
                header("Location:" . $_SESSION['old_url']);
            } else {
                header("Location:index.php");
            }
        }

    }

}else if(isset($_SESSION['username'])) {

     header("Location:index.php");

}else if($_SERVER['REQUEST_METHOD']=='GET'){ 

    if(isset($_GET['reg_complete'])) { ?>
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

include('footer.php'); 

?>