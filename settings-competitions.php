<?php
$title="Gestione Competizioni";
include('header.php');


    if(isset($_SESSION['username'])){

        $username=$_SESSION['username'];
        $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);

        $user=$database->getUserByUsername($username);

        $config=$database->dumpConfig();


        if($user->getAuth()==1){

	        $name="";
	        $first_round=0;
	        $num_rounds=0;
	        $id=-1;

        if(isset($_GET['delete'])){
            $id=$_GET['delete'];
            $database->deleteCompetition($id);
        }else if(isset($_POST['modified']) && isset($_POST['name']) && isset($_POST['first_round']) && isset($_POST['num_rounds'])){
            $name=$_POST['name'];
            $first_round=$_POST['first_round'];
            $num_rounds=$_POST['num_rounds'];
            if(isset($_POST['id']) && isset($_POST['users'])){
                $id=$_POST['id'];
                $users_in_competition=$_POST['users'];
                $database->editCompetition($id,$name,$first_round,$num_rounds);
                $database->setUsersInCompetition($id,$users_in_competition);
            }
        }else if(isset($_POST['create']) && isset($_POST['name']) && isset($_POST['first_round']) && isset($_POST['num_rounds'])){
            $name=$_POST['name'];
            $first_round=$_POST['first_round'];
            $num_rounds=$_POST['num_rounds'];
            
        }



        if(isset($_GET['edit'])){
            $edit=$_GET['edit'];
            $competition=$database->getCompetition($edit);
            $first_round=$competition->getFirstRound();
            $gen_rounds=range($first_round,38);
            $users=$database->getUsers();
            
?>
            <form action="settings-competitions.php" method="post">
                <div class="main">
                    <input type="hidden" name="id" <?php echo "value=\"".$competition->getId()."\" "; ?> >
                    <div class="setting_item">
                        <div class="setting_item_name">Modifica Competizione</div>
                    </div>
                    <div class="setting_item">
                        <div class="setting_item_descript">Nome Competizione</div>
                        <input class="setting_item_input" type="text" name="name" class="market-select" <?php echo "value=\"".$competition->getName()."\" "; ?> >
                    </div>
                    <div class="setting_item">
                        <div class="setting_item_descript">Prima Giornata di Serie A</div>
                        <input class="setting_item_input" type="hidden" name="first_round" class="market-select" <?php echo "value=\"".$competition->getFirstRound()."\" "; ?> >
                    </div>
                    <div class="setting_item">
                        <div class="setting_item_descript">Numero Giornate</div>
                        <input class="setting_item_input" type="hidden" name="num_rounds" class="market-select" <?php echo "value=\"".$competition->getNumRounds()."\" "; ?>>
                    </div>
                </div>

                <div class="main competition_creation">
                    <div class="half_size">
                        <h3>Partecipanti</h3>
                        <div id="number_users_selected">0</div>
                    </div>
                    <div class="half_size">
                        <div id="users">
                            <div id="search_box">
                                <div id="search_element">
                                    <input class="search" id="search-element" placeholder="Cerca">
                                </div>
                            </div>
                            <input type="button" id="select_all" value="Seleziona Tutti" />
                            <input type="button" id="deselect_all" value="Deseleziona Tutti" />
                            <ul class="list">
                            <?php foreach($users as $team){ ?>
                                <li class="squaredFour">
                                    <input type="checkbox" class="select_teams" <?php echo "value=\"".$team->getId()."\""; ?> name="users[]"
                                           
                                           <?php if($database->isUserInCompetition($team->getId(),$competition->getId())) echo "checked=\"checked\""; ?>
                                           
                                           >
                                    <label for="squaredFour"></label>
                                    <div class="user_name"><?php echo $team->getNameTeam(); ?></div>
                                </li>
                            <?php } ?>
                            </ul>
                        </div>

                        <input class="setting_item_input" name="modified" type="submit" value="Modifica" >
                    </div>    
                </div>
            </form>

        <?php
        }else if(isset($_POST['create'])){
	        $gen_rounds=range($first_round,38);
        ?>

        <div class="main">
            <form action="creation-competition.php" method="post">
                <div class="setting_item">
                    <div class="setting_item_name">Crea Competizione</div>
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Nome Competizione</div>
                    <input class="setting_item_input" type="text" name="name" class="market-select" <?php echo "value=\"".$name."\" "; ?> >
                </div>
				 <div class="setting_item">
                    <div class="setting_item_descript">Prima Giornata di Serie A</div>
                    <input class="setting_item_input" type="text" name="first_round" class="market-select" <?php echo "value=\"".$first_round."\" "; ?> readonly="readonly">
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Numero Giornate</div>
                    <input class="setting_item_input" type="text" name="num_rounds" class="market-select" <?php echo "value=\"".$num_rounds."\" "; ?> readonly="readonly">
                </div>

                <div class="setting_item">
	                <div class="setting_item_input">
			            <div class="setting_item_element" type="text" class="market-select" >Round Serie A</div>
	                </div>
                </div>
                

                <?php
	                for($i=0;$i<$num_rounds;$i++){
		        ?>
		        <div class="setting_item">
                    <div class="setting_item_descript">Giornata <?php echo $i+1; ?></div>
                    <div class="setting_item_input">
	                    <select class="setting_item_element market-select selection_round" name="rounds[]">
		                    <option value=""></option>
		                    <?php foreach($gen_rounds as $ele){ ?>
			                    <option <?php echo "value=\"".$ele."\""; if($ele==$first_round) { echo "selected=\"selected\""; } ?> ><?php echo $ele; ?></option>
		                   <?php }   $first_round++;  ?>
	                    </select>
                    </div>
                </div>

		        <?php	reset($gen_rounds);
	                }
                ?>



                <div class="setting_item">
                    <input class="setting_item_input" type="submit" value="Crea" >
                </div>
            </form>
        </div>

        <?php }else{ ?>
        <div class="main">
            <?php
            $competitions=$database->getCompetitions();
            foreach($competitions as $competition){ ?>
                <div class="setting_item">
                    <div class="setting_item_name"><?php echo $competition->getName(); ?></div>
                   <!--  <div class="setting_item_descript"></div> -->
                    <form action="settings-competitions.php" method="get">
                        <input class="setting_item_input" type="submit" value="Modifica" >
                        <input type="hidden" name="edit" <?php echo "value=\"".$competition->getId()."\""; ?> >
                    </form>
                    <form  action="settings-competitions.php" method="get">
                        <input class="setting_item_input" type="submit" value="Cancella" >
                        <input type="hidden" name="delete" <?php echo "value=\"".$competition->getId()."\""; ?> >
                    </form>
                </div>
            <?php } ?>
        </div>

        <div class="main">
            <form action="settings-competitions.php" method="post">
                <input type="hidden" name="create" value="1">
                <div class="setting_item">
                    <div class="setting_item_name">Crea una nuova Competizione</div>
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Nome</div>
                    <input class="setting_item_input" type="text" name="name" class="market-select">
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Prima Giornata di Serie A</div>
                    <input class="setting_item_input" type="text" name="first_round" class="market-select" >
                </div>
				<div class="setting_item">
                    <div class="setting_item_descript">Numero Giornate</div>
                    <input class="setting_item_input" type="text" name="num_rounds" class="market-select" >
                </div>
                
                <div class="setting_item">
                    <input class="setting_item_input" type="submit" value="Crea" >
            </form>
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