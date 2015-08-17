<?php
	$title = "Registrazione";
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
    
    /*$ch=curl_init("https://www.google.com/recaptcha/api/siteverify");

    $dataPost=array("secret" => "6LeL2goTAAAAAPImTDI5fKAQQd8ZfG73uCg1RMWj" , "response" => $_POST["g-recaptcha-response"]);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $json=curl_exec($ch);

    $captcha = json_decode($json,true);*/
    
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
	        "password" => $pass1 , 
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
		    if(isset($json["data"]["id"])){
			    
			    $id = $json["data"]["id"];
			    
			    if(isset($_POST["avatar"])){
					$url = $_POST["avatar"];
					
					if($url!=null){
						$editConfig= array("avatar" => "http://www.fantazenit.it/".$url);
						$params = array("postParams" => $editConfig);
				        
			        	$json = $apiAccess->accessApi("/users/".$id."/avatar","POST",$params);
			        	if($json["error"]==true){
				        	$error_json[] = $json;
			        	}
			        }
				}
			    
			    session_destroy();
				header("Location:login.php?reg_complete");
		    }else{
			    $error_json[] = $json;
		    }
	        
	    }
	    
	    //$user=$database_users->signupUser(new User(-1,$username,$name,$surname,$pass,$email,NULL,0,$balance,NULL,NULL,$name_team,$telephone,$url_fb));
	       
	    
	}else{
	    $_SESSION['wrong_pass']=true;
	    header("Location:signup.php");
	}
	

    
} ?>

    <?php if(isset($_SESSION['wrong_pass'])){ 
		$error_messages[] = "Errore: Le due password non coincidono";    
	} ?>
    
    <?php if(isset($_SESSION['wrong_username'])){ 
		$error_messages[] = "Errore: Username o Email già utilizzati";    
     } ?>
     
    <?php
	     include('error-box.php');
	?>
	
	<div class="form-horizontal signup_form">
		<h3>Registrazione al Fanta Zenit</h3>
		<div class="form-group">
		    
		    <label class="col-md-10 control-label" style="text-align: left;">Carica la Tua Immagine di Profilo<small></small></label>
	
	
	        <div class="col-md-2" id="crop-avatar">
				    			    
				<!-- Current avatar -->
			    <div class="avatar-view" title="Change the avatar">
			      <img <?php echo "src=\"img/default_avatar.png\""; ?> alt="Avatar">
			    </div>
			
			    <!-- Cropping modal -->
			    <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
			      <div class="modal-dialog modal-lg">
			        <div class="modal-content">
			          <form class="avatar-form" action="crop.php" enctype="multipart/form-data" method="post">
			            <div class="modal-header">
			              <button class="close" data-dismiss="modal" type="button">&times;</button>
			              <h4 class="modal-title" id="avatar-modal-label">Cambia Immagine</h4>
			            </div>
			            <div class="modal-body">
			              <div class="avatar-body">
			
			                <!-- Upload image and data -->
			                <div class="avatar-upload">
			                  <input class="avatar-src" name="avatar_src" type="hidden">
			                  <input class="avatar-data" name="avatar_data" type="hidden">
			                  <label for="avatarInput">Local upload</label>
			                  <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
			                </div>
			
			                <!-- Crop and preview -->
			                <div class="row">
			                  <div class="col-md-9">
			                    <div class="avatar-wrapper"></div>
			                  </div>
			                  <div class="col-md-3">
			                    <div class="avatar-preview preview-lg"></div>
			                    <div class="avatar-preview preview-md"></div>
			                    <div class="avatar-preview preview-sm"></div>
			                  </div>
			                </div>
			
			                <div class="row avatar-btns">
			                  <div class="col-md-9">
			                    <div class="btn-group">
			                      <button class="btn btn-primary" data-method="rotate" data-option="-90" type="button" title="Rotate -90 degrees">Rotate Left</button>
			                      <button class="btn btn-primary" data-method="rotate" data-option="-15" type="button">-15deg</button>
			                      <button class="btn btn-primary" data-method="rotate" data-option="-30" type="button">-30deg</button>
			                      <button class="btn btn-primary" data-method="rotate" data-option="-45" type="button">-45deg</button>
			                    </div>
			                    <div class="btn-group">
			                      <button class="btn btn-primary" data-method="rotate" data-option="90" type="button" title="Rotate 90 degrees">Rotate Right</button>
			                      <button class="btn btn-primary" data-method="rotate" data-option="15" type="button">15deg</button>
			                      <button class="btn btn-primary" data-method="rotate" data-option="30" type="button">30deg</button>
			                      <button class="btn btn-primary" data-method="rotate" data-option="45" type="button">45deg</button>
			                    </div>
			                  </div>
			                  <div class="col-md-3">
			                    <button class="btn btn-primary btn-block avatar-save" type="submit">Salva</button>
			                  </div>
			                </div>
			              </div>
			            </div>
			            <!-- <div class="modal-footer">
			              <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
			            </div> -->
			          </form>
			        </div>
			      </div>
			    </div><!-- /.modal -->
			
			    <!-- Loading state -->
			    <div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
			      
		    </div>
		</div>
	</div>

    <form action="signup.php" id="signup_form" name="signup" class="signup_form form-horizontal" method="post">
        
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
            <label class="col-md-4 control-label">Accetti il Trattamento della privacy<small> <a href="privacypolicy.php">Link</a></small></label>
            <div class="col-md-8">
              <input type="checkbox" class="form-control" id="privacy" name="privacy" >
            </div>
        </div>
        
        <!--<div class="form-group">
	        <div class="col-md-12">
		        <div class="g-recaptcha" data-sitekey="6LeL2goTAAAAAB-xcs59MMmVWWkpmZkY86HnmhF1"></div>
	        </div>
        </div> -->

        <div class="form-group">
            <div class="col-md-6">
                <a  class="col-md-12" href="login.php">Sei già registrato? Effettua il Login</a>
            </div>

            <div class="col-md-6">
                <button type="submit" disabled="true" id="reg_button" class="btn btn-default col-md-12">Registrati</button>
            </div>
        </div>
        
        
        
    </form>
    
    
    
<script src="js/crop_signup.js"></script>


<?php
    


?>

<script>

    $("body").on('change', 'input', buttonSignup);
</script>

<?php include('footer.php'); ?>