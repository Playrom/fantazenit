<?php
$title="Gestione News";
include('header.php');


    if($username!=null){

        if($userAuth>0){

	        $name="";
	        $first_round=0;
	        $num_rounds=0;
	        $id=-1;

	        if(isset($_GET['delete'])){
	
	            $id=$_GET['delete'];
	            $json=$apiAccess->accessApi("/news/$id","DELETE");
	            
	        }else if(isset($_POST['edit']) ){
	
	            $id=$_POST['id'];
	            $title=$_POST['title'];
	            $html=$_POST['html'];
	
	                
                $arr_data = array("id" => $id , "title" => $title , "html" => $html);
                     
				$params = array('postParams' => $arr_data);
				
                
                $json=$apiAccess->accessApi("/news/$id","PUT",$params);
                
                if($json["error"]==true){
	                $error_json[] = $json;
	            }
	                
	
	        }
	
	
	
	        if(isset($_GET['edit'])){
	            $edit=$_GET['edit'];
	            
	            $news = null;
	            $title = null ;
				$id = null;
				$html = null;
				
				$json=$apiAccess->accessApi("/news/$edit","GET");
	                
	            if($json["error"]==true){
		            $error_json[] = $json;
	            }else{
		            $title = $json["data"]["title"];
		            $id = $json["data"]["id"];
		            $html = $json["data"]["html"];
	            }
	            
	            
	            include('error-box.php');
	            
	?>
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
				            <form action="settings-news.php" method="post" class="form-horizontal">
				                <div class="main">
				                    <input type="hidden" name="id" <?php echo "value=\"".$id."\" "; ?> >
				
				                    <div class="form-group">
				                        <label class="col-md-12">Modifica Notizia</label>
				                    </div>
				
				                    <div class="form-group">
				                        <label class="col-md-4">Titolo</label>
				                        <div class=" col-md-8">
				                            <input class="form-control" type="text" name="title"  <?php echo "value=\"".$title."\" "; ?> >
				                        </div>
				                    </div>
				                    
				                    <div class="form-group">
					                    <label class="col-md-12">Testo</label>
			                        	<div class="col-md-12">
						                    <textarea class="form-control" id="textmce" type="text" name="html" <?php echo "value=\"".$html."\" "; ?>  ><?php echo $html; ?></textarea>
						                </div>
			                        </div>
		
				                    
				                    <div class="form-group">
					                    <div class=" col-md-12">
					                    	<button type="submit" name="edit" class="btn btn-default col-md-12">Modifica</button>
					                    </div>
					                </div>
					                
	
				
				                    
				                </div>
				
				                
				                </div>
				            </form>
				            </div>
				        </div>
				    </div>
				</div>
	
	        <?php
	        }else{
		        
		        if(isset($_POST['create'])){
			        
			        $title=$_POST['title'];
		            $html = $_POST['html'];
		            		            
		            $arr_data = array("title" => $title , "html" => $html);
		                     
					$params = array('postParams' => $arr_data);
		            
		            $json=$apiAccess->accessApi("/news","POST",$params);
		            
		            $id = null;
		            
		            if($json["error"]==true){
		                $error_json[] = $json;
		            }
		        
			    }
			    
		        $json=$apiAccess->accessApi("/news","GET");
		        
		        $news= null;
		        $error = null;
		            
		        if($json["error"]==true){
		           //var_dump($json);
		           $error = true;
		           $error_json[] = $json;
		        }else{
		            $news = $json["data"];
		        }
		        include('error-box.php');
		        
		        ?>
		        
		        <div class="container-fluid">
		        
			        <?php
			        if($error == null){ ?>
		            
						<div class="row">
							<div class="col-md-12">
								<div class="main">
		            
						            <?php 
							        foreach($news as $item){ ?>
						                   <!--  <div class="setting_item_descript"></div> -->
						                    <div class="form-group">
						                        <h3 class="col-md-8 control-label left-label"><?php echo $item["title"]; ?></h3>
						
						                        <form class="form-horizontal" action="settings-news.php" method="get">
						
						                            <div class="col-md-2">
						                                <button type="submit" class="btn btn-default col-md-12">Modifica</button>
						                            </div>
						                            <input type="hidden" name="edit" <?php echo "value=\"".$item["id"]."\""; ?> >
						
						                        </form>
						
						                        <form class="form-horizontal" action="settings-news.php" method="get">
						
						                            <div class="col-md-2">
						                                <button type="submit" class="btn btn-default col-md-12">Cancella</button>
						                            </div>
						
						                            <input type="hidden" name="delete" <?php echo "value=\"".$item["id"]."\""; ?> >
						                        </form>
						                    </div>
						            <?php 
							            }
							        ?>
								</div>
							</div>
						</div>
		            
		            
					<?php } ?>
		
			        
			        
			        <div class="row">
						<div class="col-md-12">
				            <form action="settings-news.php" method="post" class="form-horizontal">
				                <div class="main">
				
				                    <div class="form-group">
				                        <label class="col-md-12">Crea Notizia</label>
				                    </div>
				
				                    <div class="form-group">
				                        <label class="col-md-4">Titolo</label>
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
					                    	<button type="submit" name="create" class="btn btn-default col-md-12">Crea</button>
					                    </div>
					                </div>
		
				
				                    
				                </div>
				
				                
				            </form>
			            </div>
			        </div>
		        </div>
		    <?php 
			}
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