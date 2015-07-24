<?php
$title="Impostazioni Mercato";
include('header.php');


if($username!=null){
    
    
    if($userAuth==1){ 
    
	    if(isset($_GET['delete'])){
	
	        $id=$_GET['delete'];
	        $json=$apiAccess->accessApi("/markets/$id","DELETE");
	
	    }else if(isset($_POST['start_date']) && isset($_POST['finish_date']) && isset($_POST['max_change']) && isset($_POST['name'])){
	
	        $start_date=$_POST['start_date'];
	        $finish_date=$_POST['finish_date'];
	        $max_change=$_POST['max_change'];
	        $name=$_POST['name'];
	        
	        $arr_data = array( "name" => $name , "start_date" => $start_date , "finish_date" => $finish_date , "max_change" => $max_change);
                     
			$params = array('postParams' => $arr_data);
	
	        if(isset($_POST['id'])){
	
	            $id=$_POST['id'];
                
                $json=$apiAccess->accessApi("/markets/$id","PUT",$params);
                
                if($json["error"]==true){
	                var_dump($json);
	            }
	            
	            $database_markets->editMarket($id,$name,$max_change,$start_date,$finish_date);
	
	        }else{
                
                $json=$apiAccess->accessApi("/markets","POST",$params);
                
                if($json["error"]==true){
	                var_dump($json);
	            }
		        
	            $database_markets->createMarket($name,$max_change,$start_date,$finish_date);
	        }
	    }
    
    
    

	    if(isset($_GET['edit'])){
	        $edit=$_GET['edit'];
	        
	        $market = null;
	        
	        $json=$apiAccess->accessApi("/markets/$edit","GET");
            
            if($json["error"]==true){
                var_dump($json);
            }else{
	            $market = $json["data"];
            }
	        
	        if($market!=null){
	?>
	            
		        <div class="main">
		            <form class="form-horizontal" action="settings-market.php" method="post">
		                <input type="hidden" name="id" <?php echo "value=\"".$market["id"]."\" "; ?> >
		                
		                <div class="form-group">
		                    <label class="col-md-12">Modifica Mercato</label>
		                </div>
		
		                <div class="form-group">
		                    <label class="col-md-4">Nome Competizione</label>
		                    <div class=" col-md-8">
		                        <input class="form-control" type="text" name="name"  <?php echo "value=\"".$market["name"]."\" "; ?> >
		                    </div>
		                </div>
		
		                <div class="form-group">
		                    <label class="col-md-4">Data Inizio</label>
		                    <div class=" col-md-8">
		                        <input class="form-control" type="datetime" id="datetimepicker" name="start_date"  <?php echo "value=\"".$market["start_date"]."\" "; ?> >
		                    </div>
		                </div>
		
		                <div class="form-group">
		                    <label class="col-md-4">Data Fine</label>
		                    <div class=" col-md-8">
		                        <input class="form-control" type="datetime" id="datetimepicker_finish" name="finish_date"  <?php echo "value=\"".$market["finish_date"]."\" "; ?> >
		                    </div>
		                </div>
		
		                <div class="form-group">
		                    <label class="col-md-4">Numero Massimo di Cambi</label>
		                    <div class=" col-md-8">
		                        <input class="form-control" size="2" type="text" name="max_change" <?php echo "value=\"".$market["max_change"]."\" "; ?> >
		                    </div>
		                </div>
		
		                <div class="form-group">
		                    <div class="col-md-12">
		                        <button type="submit" class="btn btn-default col-md-12">Modifica</button>
		                    </div>
		                </div>
		
		            </form>
		        </div>
		        
		    <?php
			}else{
				header("Location:settings-market.php");
			}
	
        }else{
        ?>
	        
	        <div class="main">
	            <div class="container-fluid">
	                <?php 
		                
		            $markets = null;
			        
			        $json=$apiAccess->accessApi("/markets","GET");
		            
		            if($json["error"]==true){
		                var_dump($json);
		            }else{
			            $markets = $json["data"];
		            }
			        
			        if($markets!=null){
		                
		                foreach($markets as $market){ ?>
		                    <div class="row">
		
		                        <div class="col-md-8"><?php echo $market["name"]; ?>
		                            <div class="setting_item_descript">Dal <?php echo $market["start_date"]; ?> Fino al <?php echo $market["finish_date"]; ?>
		                            ---- <?php echo $market["max_change"];?> Cambi</div>
		                        </div>
		
		                        <form action="settings-market.php" method="get">
		
		                            <div class="form-group">
		                                <div class="col-md-2">
		                                    <button type="submit" class="btn btn-default col-md-12">Modifica</button>
		                                </div>
		                            </div>
		
		                            <input type="hidden" name="edit" <?php echo "value=\"".$market["id"]."\""; ?> >
		
		                        </form>
		
		                        <form  action="settings-market.php" method="get">
		
		                            <div class="form-group">
		                                <div class="col-md-2">
		                                    <button type="submit" class="btn btn-default col-md-12">Cancella</button>
		                                </div>
		                            </div>
		
		                            <input type="hidden" name="delete" <?php echo "value=\"".$market["id"]."\""; ?> >
		                        </form>
		
		                    </div>
		                <?php 
			            
			            }
			        }
			       	
			       	?>
	            </div>
	        </div>
	            
	        <div class="main">
	            <form class="form-horizontal" action="settings-market.php" method="post">
	                
	                <div class="form-group">
	                    <label class="col-md-12">Crea Mercato</label>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Nome Competizione</label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="text" name="name"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Data Inizio</label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="datetime" id="datetimepicker" name="start_date"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Data Fine</label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="datetime" id="datetimepicker_finish" name="finish_date"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Numero Massimo di Cambi</label>
	                    <div class=" col-md-8">
	                        <input class="form-control" size="2" type="text" name="max_change"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <div class="col-md-12">
	                        <button type="submit" class="btn btn-default col-md-12">Crea</button>
	                    </div>
	                </div>
	                
	            </form>
	        </div>
	        <?php
		} // FINE EDIT
    } // FINE USER AUTH

}else{
    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");
}

?>


<?php include('footer.php'); ?>