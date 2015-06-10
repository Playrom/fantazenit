<?php
session_start();

if($_SERVER['REQUEST_METHOD']=='POST'){
    
    if(isset($_POST['destroy'])){
        unset($_SESSION['token']);
    }

    if(isset($_POST['username']) && isset($_POST['password'])){

        $user=$_POST['username'];
        $pass=$_POST['password'];


        $encoded_pass=md5($pass);

        $data = array();
        
        $data['username']=$user;
        $data['password']=$encoded_pass;
        
        $data= json_encode($data);
        
        $ch = curl_init('http://associazionezenit.it/fantazenit/api/v1/login');                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data))                                                                       
        );

        $json = curl_exec($ch);

        $result = json_decode($json,true);

        if(isset($result['apiKey'])){
            $token=$result['apiKey'];
            $_SESSION['token']=$token;
        }
    }
}

if(isset($_SESSION['token'])){ $token=$_SESSION['token'] ?>
    Token:<?php echo $token; ?>
    <form action="" class="login_form form-horizontal" method="post">
        <button type="submit"  name="destroy" class="btn btn-default col-md-12">Logout</button>
    </form><br>
    
<?php

    $ch = curl_init('http://associazionezenit.it/fantazenit/api/v1/me');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(  
        'Token: '.$token
    ));
    
    $json = curl_exec($ch);

    $data=json_decode($json,true);


}else{

?>

<form action="" class="login_form form-horizontal" method="post">
    <div class="form-group">
        <label class="col-md-2 control-label">Username</label>
        <div class="col-md-10">
          <input type="text" class="form-control" id="user" name="username" placeholder="Username" >
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-2 control-label">Password</label>
        <div class="col-md-10">
          <input type="password" class="form-control" id="pass" name="password" placeholder="Password">
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
