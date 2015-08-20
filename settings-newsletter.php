<?php
$title="Invio Newsletter";
include('header.php');


    if($username!=null){

        if($userAuth>0){

	        $name="";
	        $first_round=0;
	        $num_rounds=0;
	        $id=-1;

	        
		        
	        if(isset($_POST['create'])){
		        
		        $title=$_POST['title'];
	            $html = $_POST['html'];
	            		            
	            $arr_data = array("title" => $title , "text" => $html);
	                     
				$params = array('postParams' => $arr_data);
	            
	            $json=$apiAccess->accessApi("/newsletters","POST",$params);
	            
	            $id = null;
	            
	            if($json["error"]==true){
	                $error_json[] = $json;
	            }
	        
		    }
		    
	       
			include('error-box.php');
	        
	        ?>
	        
	        <div class="container-fluid">
	        
		        			        
		        
		        <div class="row">
					<div class="col-md-12">
			            <form action="settings-newsletter.php" method="post" class="form-horizontal">
			                <div class="main">
			
			                    <div class="form-group">
			                        <label class="col-md-12">Crea Newsletter</label>
			                    </div>
			
			                    <div class="form-group">
			                        <label class="col-md-4">Titolo Email</label>
			                        <div class=" col-md-8">
			                            <input class="form-control" type="text" name="title" >
			                        </div>
			                    </div>
			                    
			                    <div class="form-group">
				                    <label class="col-md-12">Testo</label>
		                        	<div class="col-md-12">
					                    <textarea class="form-control" id="textmce" type="text" name="html"  ></textarea>
					                </div>
		                        </div>
	
			                    
			                    <div class="form-group">
				                    <div class=" col-md-12">
				                    	<button type="submit" name="create" class="btn btn-default col-md-12">Invia</button>
				                    </div>
				                </div>
	
			
			                    
			                </div>
			
			                
			            </form>
		            </div>
		        </div>
	        </div>
		    <?php 
        }else{
	        $error_messages[] = "Non hai i requisiti per accedere a questa pagina";
	        
	        include('error-box.php');
        }

    }else{
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    }

?>


<script>

    $("body").on('change', 'input[type="checkbox"]', count_check);
    $("body").on('click', '#select_all', select_all_teams);
    $("body").on('click', '#deselect_all', deselect_all_teams);


</script>


<?php include('footer.php'); ?>