<?php
$title="Gestione Competizioni";
include('header.php');


    if($username!=null){

        if($userAuth==1){

	        $name="";
	        $first_round=0;
	        $num_rounds=0;
	        $id=-1;

        if(isset($_GET['delete'])){

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
	            $_SESSION["new"] = true;
	            
	            header("Location: settings-create-direct.php");
            }
        }



        if(isset($_GET['edit'])){
            $edit=$_GET['edit'];
            
            $competition = null;
            $first_round = null ;
			$users = null;
			$ids = null;
			
			$json=$apiAccess->accessApi("/competitions/$edit","GET");
                
            if($json["error"]==true){
	            $error_json[] = $json;
            }else{
	            $competition = $json["data"]["competition"];
	            $ids = $json["data"]["ids"];
	            $first_round = $competition["first_round"];
            }
                                    
            $json=$apiAccess->accessApi("/users","GET");
                
            if($json["error"]==true){
	            $error_json[] = $json;
            }else{
	            $users = $json["data"];
            }
            
            $gen_rounds=range($first_round,38);
            
            include('error-box.php');
            
?>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
			            <form action="settings-competitions.php" method="post" class="form-horizontal">
			                <div class="main">
			                    <input type="hidden" name="id" <?php echo "value=\"".$competition["id"]."\" "; ?> >
			
			                    <div class="form-group">
			                        <label class="col-md-12">Modifica Competizione</label>
			                    </div>
			
			                    <div class="form-group">
			                        <label class="col-md-4">Nome Competizione</label>
			                        <div class=" col-md-8">
			                            <input class="form-control" type="text" name="name"  <?php echo "value=\"".$competition["name"]."\" "; ?> >
			                        </div>
			                    </div>
			
			                    <input class="setting_item_input" type="hidden" name="first_round" class="market-select" <?php echo "value=\"".$competition["first_round"]."\" "; ?> >
			                    <input class="setting_item_input" type="hidden" name="num_rounds" class="market-select" <?php echo "value=\"".$competition["num_rounds"]."\" "; ?>>
			                    
			                </div>
			
			                <div class="main competition_creation">
			                    
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
			                                        <input class="select_teams" type="checkbox" <?php echo "value=\"".$team["id"]."\""; ?> name="users[]" 
			                                        <?php if($ids!=null && in_array($team["id"], $ids)) echo "checked=\"checked\""; ?> >
			                                        <?php echo $team["name_team"]; ?>
			                                      </label>
			                                    </div>
			                                <?php } ?>
			                        </ul>
			
			                        <div class="form-group">
			                        	<div class="form-group col-md-12  input-lg">
						                    <button type="submit" name="modified" class="btn btn-default col-md-12">Modifica</button>
						                </div>
			                        </div>
			                    </div>    
			                </div>
			            </form>
			            </div>
			        </div>
			    </div>

        <?php
        }else if(isset($_POST['create'])){
	        $gen_rounds=range($first_round,38);
	        include('error-box.php');
        ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
			        <div class="main">
			            <form action="creation-competition.php" method="post">
			                <div class="form-group">
			                    <label>Crea una nuova Competizione</label>
			                </div>
			
			                    <input type="hidden" name="create" value="1">
			                    
			                    <div class="form-group col-md-3">
			                        <label>Nome</label>
			                        <input class="form-control" type="text" name="name" <?php echo "value=\"".$name."\""; ?> >
			                    </div>
			                    
			                    <div class="form-group col-md-3">
			                        <label>1° Giornata</label>
			                        <input class="form-control" type="text" name="first_round" <?php echo "value=\"".$first_round."\""; ?> readonly="readonly" >
			                    </div>
			
			                    <div class="form-group col-md-3">
			                        <label>Numero Giornate</label>
			                        <input class="form-control" type="text" name="num_rounds" <?php echo "value=\"".$num_rounds."\""; ?> readonly="readonly" >
			                    </div>
			                    
			                    <div class="form-group col-md-3">
			                        <labelTipo</label>
			                        <input class="form-control" type="text" name="type" <?php echo "value=\"".$type."\""; ?> readonly="readonly" >
			                    </div>
			                    
								
			                
			
			                <?php
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
				                }
			                ?>
			
			
			
			                <div class="form-group col-md-12  input-lg">
			                    <button type="submit" class="btn btn-default col-md-12">Crea</button>
			                </div>
			            </form>
			        </div>
			    </div>
			</div>
		</div>

        <?php }else{ ?>
            <?php
	            
	        $json=$apiAccess->accessApi("/competitions","GET");
	        
	        $competitions = null;
	        $error = null;
                
            if($json["error"]==true){
               //var_dump($json);
               $error = true;
               $error_json[] = $json;
            }else{
	            $competitions = $json["data"];
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
						        foreach($competitions as $competition){ ?>
					                   <!--  <div class="setting_item_descript"></div> -->
					                    <div class="form-group">
					                        <h3 class="col-md-8 control-label left-label"><?php echo $competition["name"]; ?></h3>
					
					                        <form class="form-horizontal" action="settings-competitions.php" method="get">
					
					                            <div class="col-md-2">
					                                <button type="submit" class="btn btn-default col-md-12">Modifica</button>
					                            </div>
					                            <input type="hidden" name="edit" <?php echo "value=\"".$competition["id"]."\""; ?> >
					
					                        </form>
					
					                        <form class="form-horizontal" action="settings-competitions.php" method="get">
					
					                            <div class="col-md-2">
					                                <button type="submit" class="btn btn-default col-md-12">Cancella</button>
					                            </div>
					
					                            <input type="hidden" name="delete" <?php echo "value=\"".$competition["id"]."\""; ?> >
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
						<div class="main">
		           
				            <div class="form-group">
				                <label>Crea una nuova Competizione</label>
				            </div>
				
				            <form class="form-inline" action="settings-competitions.php" method="post">
				                <input type="hidden" name="create" value="1">
				                
				                <div class="form-group col-md-3">
				                    <label>Nome</label>
				                    <input class="form-control" type="text" name="name">
				                </div>
				                
				                <div class="form-group col-md-3">
				                    <label>1° Giornata</label>
				                    <input class="form-control" type="text" name="first_round">
				                </div>
				
								<div class="form-group col-md-3">
				                    <label>Numero Giornate</label>
				                    <input class="form-control" type="text" name="num_rounds">
				                </div>
				                
				                <div class="form-group col-md-3">
					                <select class="form-control selection_round" name="type">
					                    <option value="CHAMPIONSHIP">Championship</option>
					                    <option value="DIRECT">Direct</option>
					                </select>
				                </div>
				                
				                <div class="form-group col-md-3  input-lg">
				                    <button type="submit" class="btn btn-default col-md-12">Crea</button>
				                </div>
				
				            </form>
						</div>
					</div>
		        </div>
	        </div>
	    <?php }
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