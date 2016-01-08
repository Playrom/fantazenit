<?php
$title="Gestione Competizioni Dirette";
include('header.php');


    if($username!=null){

        if($userAuth==1){

	        $name="";
	        $first_round=0;
	        $num_rounds=0;
	        $id=-1;

        /*if(isset($_GET['delete'])){

            $id=$_GET['delete'];
            $json=$apiAccess->accessApi("/competitions/$id","DELETE");
            
        }else if(isset($_POST['modified']) && isset($_POST['name']) && isset($_POST['first_round']) && isset($_POST['num_rounds'])){

            $name=$_POST['name'];
            $first_round=$_POST['first_round'];
            $num_rounds=$_POST['num_rounds'];

            if(isset($_POST['id']) && isset($_POST['users'])){

                $id=$_POST['id'];
                $users_in_competition=$_POST['users'];
                
                $arr_data = array("id" => $id , "name" => $name , "first_round" => $first_round , "num_rounds" => $num_rounds , "users" => $users_in_competition);
                     
				$params = array('postParams' => $arr_data);
                
                $json=$apiAccess->accessApi("/competitions/$id","PUT",$params);
                
                if($json["error"]==true){
	                $error_json[] = $json;
	            }
                
            }

        }else if(isset($_POST['create']) && isset($_POST['name']) && isset($_POST['first_round']) && isset($_POST['num_rounds'])){

            $name=$_POST['name'];
            $first_round=$_POST['first_round'];
            $num_rounds=$_POST['num_rounds'];
            $type = $_POST['type'];
            
            if($type=="DIRECT"){
	            $_SESSION["first_round"]  = $first_round;
	            $_SESSION["name"] = $name;
	            
	            header("Location: settings-create-direct.php");
            }
        }

*/

        if(isset($_SESSION['new'])){
            $name = $_SESSION["name"];
            
                                    
            unset($_SESSION["new"]);
            
            include('error-box.php');
            
?>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
			            <form action="settings-create-direct.php" method="post" class="form-horizontal">
			                <div class="main">
			
			                   
			
			                    <div class="form-group">
			                        <label class="col-md-4">Nome Competizione</label>
			                        <div class=" col-md-8">
			                            <input class="form-control" type="text" name="name"  <?php echo "value=\"".$name."\" "; ?> >
			                        </div>
			                    </div>
			                    
			                    <div class="form-group">
			                        <label class="col-md-4">Nome Fase</label>
			                        <div class=" col-md-8">
			                            <input class="form-control" type="text" name="name_phase" >
			                        </div>
			                    </div>
			                    
			                    <div class="form-group">
				                    <label class="col-md-4">Tipo Fase</label>
					                <select class="form-control selection_round" name="type_phase">
					                    <option value="ROUND_ROBIN">Gironi</option>
					                    <option value="ROUND_ROBIN_SEEDED">Gironi con Fasce da Championship</option>
					                </select>
				                </div>
			
			                    <!--<input class="setting_item_input" type="hidden" name="first_round" class="market-select" <?php echo "value=\"".$competition["first_round"]."\" "; ?> >
			                    <input class="setting_item_input" type="hidden" name="num_rounds" class="market-select" <?php echo "value=\"".$competition["num_rounds"]."\" "; ?> > -->
			                    
			                </div>
			
			                <div class="main competition_creation">
			                        <div class="form-group">
			                        	<div class="form-group col-md-12  input-lg">
						                    <button type="submit" name="step-selection-phase" class="btn btn-default col-md-12">Avanti</button>
						                </div>
			                        </div>
			                    </div>    
			                </div>
			            </form>
			            </div>
			        </div>
			    </div>

        <?php
	    
	    }else if(isset($_POST['step-selection-phase'])){
		    $name = $_SESSION["name"];
		    $name_phase = $_POST["name_phase"];
		    $type_phase = $_POST["type_phase"];
		    $_SESSION["type_phase"] = $type_phase;
		    
		    
		
		?>    
		    <div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
			            <form action="settings-create-direct.php" method="post" class="form-horizontal">
			                <div class="main">
			
			                   
			
			                    <div class="form-group">
			                        <label class="col-md-4">Nome Competizione</label>
			                        <input class="form-control" type="text" name="name"  <?php echo "value=\"".$name."\" "; ?> >
			                    </div>
			                    
			                    <div class="form-group">
			                        <label class="col-md-4">Nome Fase</label>
			                        <div class=" col-md-8">
			                            <input class="form-control" type="text" name="name_phase" <?php echo "value=\"".$name_phase."\" "; ?>>
			                        </div>
			                    </div>
			                    
			                    <div class="form-group">
				                    <label class="col-md-4">Tipo Fase</label>
					                <input class="form-control" type="text" name="type_phase"  <?php echo "value=\"".$type_phase."\" "; ?> readonly="true" >
				                </div>
			
			                    <!--<input class="setting_item_input" type="hidden" name="first_round" class="market-select" <?php echo "value=\"".$competition["first_round"]."\" "; ?> >
			                    <input class="setting_item_input" type="hidden" name="num_rounds" class="market-select" <?php echo "value=\"".$competition["num_rounds"]."\" "; ?> > -->
			                    
			                </div>
			
			                <div class="main competition_creation">
				                
				                <?php
					                if($type_phase == "ROUND_ROBIN"){
						                
						                $users = null;
            
							            $json=$apiAccess->accessApi("/users","GET");
							                
							            if($json["error"]==true){
								            $error_json[] = $json;
							            }else{
								            $users = $json["data"];
							            }

						        ?>
			                    
			                    <div class="form-group">
			                        <label class="col-md-4">Partecipanti</label>
			                        <div class="col-md-8">
			                            <div id="number_users_selected">0</div>
			                        </div>
			                    </div>
			
			                    <div id="users" >
			                        <div id="search_box" class="form-group">
			                            <div id="search_element"  class="col-md-12">
			                                <input class="search form-control" id="search-element" placeholder="Cerca">
			                            </div>
			                        </div>
			
			                        <div class="form-group">
			                            <div class="col-md-6">
			                                <input type="button" id="select_all" value="Seleziona Tutti" class="btn btn-default col-md-12" />
			                            </div>
			
			                            <div class="col-md-6">
			                                <input type="button" id="deselect_all" value="Deseleziona Tutti" class="btn btn-default col-md-12" />
			                            </div>
			                        </div>
			
			                        <ul class="list">
			                            <div class="form-group">
			                                <?php foreach($users as $team){ ?>
			                                    <div class="checkbox-inline col-md-3" style="padding: 0;">
			                                      <label>
			                                        <input class="select_teams" type="checkbox" <?php echo "value=\"".$team["id"]."\""; ?> name="users[]"  >
			                                        <?php echo $team["name_team"]; ?>
			                                      </label>
			                                    </div>
			                                <?php } ?>
			                        </ul>
			
			                        <div class="form-group">
			                        	<div class="form-group col-md-12  input-lg">
						                    <button type="submit" name="new-step-2" class="btn btn-default col-md-12">Avanti</button>
						                </div>
			                        </div>
			                    </div>  
			                    
			                   	<?php 
				                   	}else if($type_phase == "ROUND_ROBIN_SEEDED"){
					                   	
					                   $json=$apiAccess->accessApi("/competitions","GET");
                
									   if($json["error"]==true){
									      $error_json[] = $json;
							           }else{
									      $competitions = $json["data"];
							           }
					            ?>
					            
					            	<div class="form-group">
					                    <label class="col-md-4">Competizione Fonte</label>
						                <select class="form-control selection_round" name="init_competition">
							                <?php
								                foreach($competitions as $compe){ ?>
									                <option value="<?php echo $compe["id"]; ?>"><?php echo $compe["name"]; ?></option>
								            <?php } ?>
								            						                    
						                </select>
					                </div>
					                
					                <div class="form-group">
					                    <label class="col-md-4">Numero Seed Group</label>
						                <input class="form-control" type="text" name="seed" >
					                </div>
					                
					                <div class="form-group">
					                    <label class="col-md-4">Numero Utenti</label>
						                <input class="form-control" type="text" name="usersnum" >
					                </div>
					                
					                <div class="form-group">
			                        	<div class="form-group col-md-12  input-lg">
						                    <button type="submit" name="new-step-2" class="btn btn-default col-md-12">Avanti</button>
						                </div>
			                        </div>
			                    </div>  
					            <?php
				                   	}
				                ?>  
			                </div>
			            </form>
			            </div>
			        </div>
			    </div>
   
	    	
	    <?php 
        }else if(isset($_POST['new-step-2'])){
	        
	        
	        
            $name=$_POST['name'];
            $users_in_competition=null;
            $name_phase = $_POST["name_phase"];
            $type_phase = $_SESSION["type_phase"];
            
            $number = 0;
            
            if($type_phase == "ROUND_ROBIN"){
	            if(isset($_POST['users'])){

	                $users_in_competition=$_POST['users'];
	                $number = count($users_in_competition);
	                                
	            }
            }else if($type_phase == "ROUND_ROBIN_SEEDED"){
	            
	            $users_in_competition = $_POST["seed"] * $_POST["usersnum"];
	            $number = intval($users_in_competition);
	            $init_competition = $_POST["init_competition"];
            }

            

	        include('error-box.php');
        ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
			        <div class="main">
			            <form action="settings-create-direct.php" method="post">
			                <div class="form-group">
			                    <label><?php echo $name." - ".$name_phase; ?></label>
			                </div>
			
			                    
			                    <div class="form-group col-md-12">
			                        <label>Nome Competizione</label>
			                        <input class="form-control" type="text" name="name" <?php echo "value=\"".$name."\""; ?> >
			                    </div>
			                    
			                    <div class="form-group col-md-12">
			                        <label>Nome Fase a Gironi</label>
			                        <input class="form-control" type="text" name="name_phase" <?php echo "value=\"".$name_phase."\""; ?> >
			                    </div>
			                    
			                    <div class="form-group">
			                        <label class="col-md-4">Partecipanti</label>
			                        <div class="col-md-8">
			                            <div id="number_users_selected"><?php echo $number; ?></div>
			                        </div>
			                    </div>
			                    
			                   
			                    <div class="form-group col-md-12">
			                        <label>Numero Gironi</label>
			                        <input class="form-control" type="text" name="num_groups" >
			                    </div>
			                    
			                    <select class="form-control" name="ar">
				                    <option value="1">Solo Andata</option>
				                    <option value="2">Andata e Ritorno</option>
				                    
			                    </select>
			                    
			                    <?php 
				                    
				                if($type_phase == "ROUND_ROBIN"){
					                foreach($users_in_competition as $team){ ?>
	                                    <input type="hidden" name="users[]"  value="<?php echo $team; ?>">
	                            <?php 
		                            } 
		                        }else if($type_phase == "ROUND_ROBIN_SEEDED"){
			                    	$json=$apiAccess->accessApi("/competitions/".$init_competition."/standings","GET");
                
									if($json["error"]==true){
										$error_json[] = $json;
						           	}else{
								    	$standings = $json["data"];
						           	}
						           	
						           	$start = 0;
						           	$seednum = 0;
						           	
						           	foreach($standings["standings"] as $team){
							           	if($start >= $_POST["usersnum"]) { $start = 0; $seednum++; }
							           	if($seednum >= $_POST["seed"]) break;
							           	
						           	?>
	                                    <input type="hidden" name="users[<?php echo $seednum;?>][]"  value="<?php echo $team["id_user"]; ?>">
	                            <?php 
		                            	$start++;
		                            } 

			                        
			                    }    
		                        ?>

											
			                
			
			                <?php /*
				                for($i=0;$i<$num_rounds;$i++){
					        ?>
					        <div class="form-group col-md-12">
			                    <label>Giornata <?php echo $i+1; ?></label>
			                    
			                    <select class="form-control selection_round" name="rounds[]">
				                    <option value=""></option>
				                    <?php foreach($gen_rounds as $ele){ ?>
					                    <option <?php echo "value=\"".$ele."\""; if($ele==$first_round) { echo "selected=\"selected\""; } ?> ><?php echo $ele; ?></option>
				                   <?php }   $first_round++;  ?>
			                    </select>
			                </div>
			
					        <?php	reset($gen_rounds);
				                } */
			                ?>
			
			
			
			                <div class="form-group col-md-12  input-lg">
			                    <button type="submit" name="new-step-3" class="btn btn-default col-md-12">Avanti</button>
			                </div>
			            </form>
			        </div>
			    </div>
			</div>
		</div>
		
		
		<?php
        }else if(isset($_POST['new-step-3'])){
	        
	        
	        
            $name=$_POST['name'];
            $users_in_competition=null;
            $name_phase = $_POST["name_phase"];
            $type_phase = $_SESSION["type_phase"];
            
            
            $number = 0;

            if($type_phase=="ROUND_ROBIN"){
	            if(isset($_POST['users'])){

                	$users_in_competition=$_POST['users'];
                	$number = count($users_in_competition);
                                
            	}
            }else if($type_phase=="ROUND_ROBIN_SEEDED"){
	            if(isset($_POST['users'])){

	                $users_in_competition=$_POST['users'];
	                foreach($users_in_competition as $ss){
		                $number = $number + count($ss);
	                }
	                
	                                
	            }
            }
            
            $num_groups = intval($_POST["num_groups"]);
                        
            $ar = $_POST["ar"];
            
            $num_match_for_group = ( ceil(intval($number) / $num_groups )  ) * $ar;
            
            var_dump($ar);
            
	        
	        include('error-box.php');
        ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
			        <div class="main">
			            <form action="settings-create-direct.php" method="post">
			                <div class="form-group">
			                    <label><?php echo $name." - ".$name_phase; ?></label>
			                </div>
			
			                    <input type="hidden" name="name"  <?php echo "value=\"".$name."\""; ?>  >
			                    <input type="hidden" name="name_phase"  <?php echo "value=\"".$name_phase."\""; ?>  >
			                    <input type="hidden" name="type_phase"  <?php echo "value=\"".$type_phase."\""; ?>  >
			                    <input type="hidden" name="num_groups"  <?php echo "value=\"".$num_groups."\""; ?>  >
			                    <input type="hidden" name="ar"  <?php echo "value=\"".$ar."\""; ?>  >
			                    
			                    <?php 
				                    
				                if($type_phase == "ROUND_ROBIN"){
					                foreach($users_in_competition as $team){ ?>
	                                    <input type="hidden" name="users[]" value="<?php echo $team; ?>">
	                                <?php } 
	                            }else if($type_phase == "ROUND_ROBIN_SEEDED"){
		                            $start = 0;
					                foreach($users_in_competition as $ss){
						                foreach($ss as $team){ ?>

	                                    <input type="hidden" name="users[<?php echo $start; ?>][]" value="<?php echo $team; ?>">
	                                <?php }
		                                $start++;
		                            } 
		                        }
		                        
				                for($i=0;$i<$num_match_for_group;$i++){
					        ?>
					        <div class="form-group col-md-12">
			                    <label>Giornata <?php echo $i+1; ?></label>
			                    <input class="form-control" type="text" name="rounds[]" >
			                </div>
			
					        <?php	
				                } 
			                ?>
			                
			                <?php 
				                for($i=0;$i<$num_groups;$i++){
					        ?>
					        <div class="form-group col-md-12">
			                    <label>Nome Girone <?php echo $i+1; ?></label>
			                    <input class="form-control" type="text" name="groups[]" >
			                </div>
			
					        <?php	
				                } 
			                ?>
			
			
			
			                <div class="form-group col-md-12  input-lg">
			                    <button type="submit" name="new-step-4" class="btn btn-default col-md-12">Avanti</button>
			                </div>
			            </form>
			        </div>
			    </div>
			</div>
		</div>
		
		<?php
        }else if(isset($_POST['new-step-4'])){
	        
	        
	        
            $name=$_POST['name'];
            $users_in_competition=null;
            $name_phase = $_POST["name_phase"];
			$type_phase = $_POST["type_phase"];
            
            
            $number = 0;

            if(isset($_POST['users'])){

                $users_in_competition=$_POST['users'];
                $number = count($users_in_competition);
                                
            }
            
            $num_groups = intval($_POST["num_groups"]);
                        
            $rounds = $_POST["rounds"];
            
            $name_groups = $_POST["groups"];
            
            
            
            $arr_data = array( "name" => $name , "type"=>"DIRECT" , "users" => $users_in_competition , "phase" => array("name" => $name_phase , "num_groups" => $num_groups , "rounds" => $rounds , "name_groups" => $name_groups , "type_phase" => $type_phase));
                 
			$params = array('postParams' => $arr_data);
			            
            $json=$apiAccess->accessApi("/competitions","POST",$params);
            
            if($json["error"]==true){
                $error_json[] = $json;

	        
	        include('error-box.php');
        ?>
		


        <?php }
        }

    }else{
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    }
}

?>


<script>

    $("body").on('change', 'input[type="checkbox"]', count_check);
    $("body").on('click', '#select_all', select_all_teams);
    $("body").on('click', '#deselect_all', deselect_all_teams);


</script>


<?php include('footer.php'); ?>