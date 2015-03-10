<?php
$title="Creazione Competizione";
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

            if(isset($_POST['created']) && isset($_POST['users'])){
                $users=$_POST['users'];
                $rounds=null;
                $name=$_POST['name'];
                $first_round=$_POST['first_round'];
                $num_rounds=$_POST['num_rounds'];
                
                $id=$database->createCompetition($name,$first_round,$num_rounds);
                
                if(isset($_SESSION['rounds'])) {
                    $rounds=$_SESSION['rounds'];
                    $database->setRoundsCompetition($id,$rounds);
                  unset($_SESSION['rounds']);
                }
                
                $database->setUsersInCompetition($id,$users);
                
                header("Location:settings-competitions.php");

                
            }else if(isset($_POST['name']) && isset($_POST['first_round']) && isset($_POST['num_rounds']) && isset($_POST['rounds'])){
                $name=$_POST['name'];
                $first_round=$_POST['first_round'];
                $num_rounds=$_POST['num_rounds'];
                //$id=$database->createCompetition($name,$first_round,$num_rounds);
                $rounds=$_POST['rounds'];
                //$database->setRoundsCompetition($id,$rounds);
                $_SESSION['rounds']=$rounds;
                $users=$database->getUsers();
        ?>

            <div class="main competition_creation">
                <div class="half_size">
                    <h3>Partecipanti</h3>
                    <div id="number_users_selected">0</div>
                </div>
                <form class="half_size" action="creation-competition.php" method="post">

                    <input class="setting_item_input" type="hidden" name="name" class="market-select" <?php echo "value=\"".$name."\" "; ?> >
                    <input class="setting_item_input" type="hidden" name="first_round" class="market-select" <?php echo "value=\"".$first_round."\" "; ?> readonly="readonly">
                    <input class="setting_item_input" type="hidden" name="num_rounds" class="market-select" <?php echo "value=\"".$num_rounds."\" "; ?> readonly="readonly">

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
                                <input type="checkbox" class="select_teams" <?php echo "value=\"".$team->getId()."\""; ?> name="users[]">
                                <label for="squaredFour"></label>
                                <div class="user_name"><?php echo $team->getNameTeam(); ?></div>
                            </li>
                        <?php } ?>
                        </ul>
                    </div>

                    <input class="setting_item_input" name="created" type="submit" value="Crea" >


                </form>    
            </div>

<?php
                
            }else{
                header("Location:settings-competitions.php");
            }
        
    }else{
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    } // FINE SE ADMIN
        
} // FINE USERNAME GET

?>



<script>
    
    count_check();

    $("body").on('change', 'input[type="checkbox"]', count_check);
    $("body").on('click', '#select_all', select_all_teams);
    $("body").on('click', '#deselect_all', deselect_all_teams);


</script>
<?php include('footer.php'); ?>