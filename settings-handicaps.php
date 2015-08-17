<?php
$title="Impostazioni Handicap";
include('header.php');


if($username != null){
    
    if($userAuth==1){ 
    
        if(isset($_POST['delete-handicap-round'])){
            $id_handicap=$_POST['delete-handicap-round'];
            
            $json=$apiAccess->accessApi("/handicaps/rounds/$id_handicap","DELETE");
            
            if($json["error"]==true){
	            var_dump($json);
            }
            
        }

        if(isset($_POST['delete-handicap-competition'])){
            $id_handicap=$_POST['delete-handicap-competition'];
            
            $json=$apiAccess->accessApi("/handicaps/competitions/$id_handicap","DELETE");
            
            if($json["error"]==true){
	            var_dump($json);
            }
            
            
        }
        
        if(isset($_POST['delete-bonus'])){
            $id=$_POST['delete-bonus'];
            
            
            $json=$apiAccess->accessApi("/handicaps/bonuses/$id","DELETE");
            
            if($json["error"]==true){
	            var_dump($json);
            }
            
            
        }

        if(isset($_POST['competitions-form']) && isset($_POST['team']) && isset($_POST['competition']) && isset($_POST['points']) && isset($_POST['description'])){
            $team=$_POST['team'];
            $competition=$_POST['competition'];
            $points=$_POST['points'];
            $description=$_POST['description'];
            
            
            $arr_data = array("id_team" => $team , "id_type" => $competition , "points" => $points , "description" => $description , "type" => "COMPETITION");
                     
			$params = array('postParams' => $arr_data);
            
            $json=$apiAccess->accessApi("/handicaps","POST",$params);
                        
            if($json["error"]==true){
                var_dump($json);
            }



        }

        if(isset($_POST['rounds-form']) && isset($_POST['team']) && isset($_POST['round']) && isset($_POST['points']) && isset($_POST['description'])){
            $team=$_POST['team'];
            $round=$_POST['round'];
            $points=$_POST['points'];
            $description=$_POST['description'];

            $arr_data = array("id_team" => $team , "id_type" => $round , "points" => $points , "description" => $description , "type" => "ROUND");
                     
			$params = array('postParams' => $arr_data);
            
            $json=$apiAccess->accessApi("/handicaps","POST",$params);
                        
            if($json["error"]==true){
                var_dump($json);
            }
        }
        
        if(isset($_POST['bonus-form']) && isset($_POST['team']) && isset($_POST['points']) && isset($_POST['description'])){
            $team=$_POST['team'];
            $points=$_POST['points'];
            $description=$_POST['description'];
                        
            $arr_data = array("id_team" => $team ,  "points" => $points , "description" => $description , "type" => "BONUS");
                     
			$params = array('postParams' => $arr_data);
            
            $json=$apiAccess->accessApi("/handicaps","POST",$params);
                        
            if($json["error"]==true){
                var_dump($json);
            }



        }
        
        ?>
        
        <?php 
	            
        $json=$apiAccess->accessApi("/handicaps/bonuses","GET");
            
        if($json["error"]==false){
	    ?>
	    
	        <div class="container-fluid">
		        <div class="main">
			        <?php
				        
	                $bonuses=$json["data"];
	            
	                $round=1; ?>
	                
	                <div class="row">
	                    <label class="col-md-12">Bonus Milioni</label>
	                </div>
					
					<?php	                
	                foreach($bonuses as $bonus){ ?>
	
	                    
	
	                    <div class="row">
		                    
		                    <div class="col-md-12">
	
		                        <form class="form-horizontal" action="settings-handicaps.php" method="post">
		
		                            <label class="col-md-6" style="font-weight: 300;">
		                                <?php echo $bonus["user"]["name_team"]; ?> - <?php echo $bonus["description"]; ?> : <?php echo $bonus["points"]; ?> Punti
		                            </label>
		
		                            <div class="form-group">
		                                <div class="col-md-6">
		                                    <button type="submit" class="btn btn-default col-md-12">Cancella</button>
		                                </div>
		                            </div>
		
		                            <input type="hidden" name="delete-bonus" <?php echo "value=\"".$bonus["id"]."\""; ?> >
		                        </form>
		
		                    </div>
	                    </div>
					<?php
					}
					?>
					
		        </div>
	        </div>
	    <?php 
        }
    	?>

        <?php 
	            
        $json=$apiAccess->accessApi("/handicaps/competitions","GET");
            
        if($json["error"]==false){
	    ?>
	    
	        <div class="container-fluid">
		        <div class="main">
			        <?php
	                		            
	                $handicaps_competitions=$json["data"];
	                
	                $id=1;
	                foreach($handicaps_competitions as $handicap){ 
	
	                    if(intval($handicap["competition"]["id"])>$id){
	                        $id=$handicap["competition"]["id"];
	                    ?>
	                        <div class="row">
	                            <label class="col-md-12"><?php echo $handicap["competition"]["name"];?></label>
	                        </div>
	                    <?php
	                        }
	                    ?>
	
	                    <div class="row">
		                    
		                    <div class="col-md-12">
	
		                        <form class="form-horizontal" action="settings-handicaps.php" method="post">
		
		                            <label class="col-md-6 " style="font-weight: 300;">
		                                <?php echo $handicap["user"]["name_team"]; ?> - <?php echo $handicap["description"]; ?> : <?php echo $handicap["points"]; ?> Punti
		                            </label>
		
		                            <div class="form-group">
		                                <div class="col-md-6">
		                                    <button type="submit" class="btn btn-default col-md-12">Cancella</button>
		                                </div>
		                            </div>
		
		                            <input type="hidden" name="delete-handicap-competition" <?php echo "value=\"".$handicap["id"]."\""; ?> >
		                        </form>
		                    </div>
	
	                    </div>
	               <?php
					}
					?>
					
		        </div>
	        </div>
	    <?php 
        }
    	?>

<?php 
	            
        $json=$apiAccess->accessApi("/handicaps/rounds","GET");
            
        if($json["error"]==false){
	    ?>
	    
	        <div class="container-fluid">
		        <div class="main">
			        <?php
	                		            
	                $handicaps_rounds=$json["data"];
                
	                $round=1;
	                
	                foreach($handicaps_rounds as $handicap){ 
	
	                    if($handicap["id_round"]>$round){
	                        $round=$handicap["id_round"];
	                    ?>
	                        <div class="row">
	                            <label class="col-md-12">Giornata <?php echo $round;?></label>
	                        </div>
	                    <?php
	                        }
	                    ?>
	
	                    <div class="row">
		                    
		                    <div class="col-md-12">
	
		                        <form class="form-horizontal" action="settings-handicaps.php" method="post">
		
		                            <label class="col-md-6" style="font-weight: 300;">
		                                <?php echo $handicap["user"]["name_team"]; ?> - <?php echo $handicap["description"]; ?> : <?php echo $handicap["points"]; ?> Punti
		                            </label>
		
		                            <div class="form-group">
		                                <div class="col-md-6">
		                                    <button type="submit" class="btn btn-default col-md-12">Cancella</button>
		                                </div>
		                            </div>
		
		                            <input type="hidden" name="delete-handicap-round" <?php echo "value=\"".$handicap["id"]."\""; ?> >
		                        </form>
		                    </div>
	
	                    </div>
	                 <?php
					}
					?>
					
		        </div>
	        </div>
	    <?php 
        }
    	?>
        
        <div class="container-fluid">
		    <div class="main">
	            <form class="form-horizontal" action="settings-handicaps.php" method="post">
	                
	                <div class="form-group">
	                    <label class="col-md-12">Crea Bonus Fantamilioni</label>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Squadra</label>
	                    <div class="col-md-8">
	                        <?php
		                        
		                        $json=$apiAccess->accessApi("/users","GET");
		                        $users = null;
	                
					            if($json["error"]==true){
					                var_dump($json);
					            }else{
						            $users = $json["data"];
					            }
		                        
	                        ?>
	                        <select class="form-control"  name="team" >
	                            <?php foreach($users as $team){ ?>
	                                <option <?php echo "value=\"".$team["id"]."\""; ?> ><?php echo $team["name_team"]; ?></option> 
	                            <?php } ?>
	                        </select>
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Descrizione</label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="text" name="description"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Punti <small>Inserire - per penalizzazione</small></label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="text" name="points"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <div class="col-md-12">
	                        <button type="submit" name="bonus-form" class="btn btn-default col-md-12">Crea</button>
	                    </div>
	                </div>
	                
	            </form>
		    </div>
        </div>
            
        <div class="container-fluid">
		    <div class="main">
	            <form class="form-horizontal" action="settings-handicaps.php" method="post">
	                
	                <div class="form-group">
	                    <label class="col-md-12">Crea Handicap Competizione</label>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Competizione</label>
	                    <div class="col-md-8">
	                        <?php 
		                    
		                    $json=$apiAccess->accessApi("/competitions","GET");
		        
					        $competitions = null;
				                
				            if($json["error"]==true){
				                var_dump($json);
				            }else{
					            $competitions = $json["data"];
				            }  
				            
				            if(isset($competitions[$config['default_competition']])){
					            $comp = $competitions[$config['default_competition']];
				            }  
		                        
		                    ?>
		                    
	                        <select class="form-control"  name="competition" >
	                            <?php foreach($competitions as $competition){ ?>
	                                <option <?php echo "value=\"".$competition["id"]."\""; if($comp!=null && $competition["id"]==$comp["id"]) echo " selected"; ?> ><?php echo $competition["name"]; ?></option> 
	                            <?php } ?>
	                        </select>
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Squadra</label>
	                    <div class="col-md-8">
	                        <?php
		                        
		                        $json=$apiAccess->accessApi("/users","GET");
		                        $users = null;
	                
					            if($json["error"]==true){
					                var_dump($json);
					            }else{
						            $users = $json["data"];
					            }
		                        
	                        ?>
	                        <select class="form-control"  name="team" >
	                            <?php foreach($users as $team){ ?>
	                                <option <?php echo "value=\"".$team["id"]."\""; ?> ><?php echo $team["name_team"]; ?></option> 
	                            <?php } ?>
	                        </select>
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Descrizione</label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="text" name="description"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Punti <small>Inserire - per penalizzazione</small></label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="text" name="points"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <div class="col-md-12">
	                        <button type="submit" name="competitions-form" class="btn btn-default col-md-12">Crea</button>
	                    </div>
	                </div>
	                
	            </form>
		    </div>
        </div>

        <div class="container-fluid">
		    <div class="main">
	            <form class="form-horizontal" action="settings-handicaps.php" method="post">
	                
	                <div class="form-group">
	                    <label class="col-md-12">Crea Handicap Giornata</label>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Giornata</label>
	                    <div class="col-md-8">
	                        <?php 
		                    
		                    $json=$apiAccess->accessApi("/rounds","GET");
	                        $rounds = null;
	            
				            if($json["error"]==true){
				                var_dump($json);
				            }else{
					            $rounds= $json["data"];
				            }
		                    
		                    
		                    $json=$apiAccess->accessApi("/users","GET");
	                        $users = null;
	            
				            if($json["error"]==true){
				                var_dump($json);
				            }else{
					            $users = $json["data"];
				            }    
		                        
		                    ?>
	                        <select class="form-control"  name="round" >
	                            <?php foreach($rounds as $id_round){ ?>
	                                <option <?php echo "value=\"".$id_round."\""; ?> ><?php echo $id_round; ?></option> 
	                            <?php } ?>
	                        </select>
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Squadra</label>
	                    <div class="col-md-8">
	                        <select class="form-control"  name="team" >
	                            <?php foreach($users as $team){ ?>
	                                <option <?php echo "value=\"".$team["id"]."\""; ?> ><?php echo $team["name_team"]; ?></option> 
	                            <?php } ?>
	                        </select>
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Descrizione</label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="text" name="description"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <label class="col-md-4">Punti <small>Inserire - per penalizzazione</small></label>
	                    <div class=" col-md-8">
	                        <input class="form-control" type="text" name="points"  >
	                    </div>
	                </div>
	
	                <div class="form-group">
	                    <div class="col-md-12">
	                        <button type="submit" name="rounds-form" class="btn btn-default col-md-12">Crea</button>
	                    </div>
	                </div>
	                
	            </form>
		    </div>
        </div>

        <?php 

    }
    

}else{
    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");
}

?>


<?php include('footer.php'); ?>