<?php
include('header.php');
?>


<?php if(isset($_SESSION['username'])){
    $username=$_SESSION['username'];
    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $user=$database->getUserByUsername($username);

    if($user->getAuth()==1){
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit_players"]))  {
            $uploaddir = 'uploads/';
            $date=null;
            if(isset($_POST["date"])){
            	$date=$_POST["date"];
            	var_dump($date);
            }else{
	            $date=date("now");
            }

            $uploadfile = $uploaddir . basename("quote-".$date.".html");

            if (move_uploaded_file($_FILES['players']['tmp_name'], $uploadfile)) {
                $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
                $database->loadPlayersToDatabase($uploadfile,$date);
            } else {
                echo "Possibile attacco tramite file upload!\n";
            }


        }else if(isset($_POST["submit_votes"]) && isset($_POST['round']))  {
            $uploaddir = 'uploads/';
            $round=$_POST['round'];
            $round=intval($round);
            $uploadfile = $uploaddir . basename("stat-".$round.".html");

            if (move_uploaded_file($_FILES['votes']['tmp_name'], $uploadfile)) {
                $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
                $database->loadStatsToDatabase($round,$uploadfile);
            } else {
                echo "Possibile attacco tramite file upload!\n";
            }
        }


        ?>
    <form action="loadfile.php" method="post" enctype="multipart/form-data">
    Select file players upload:
        <input type="file" name="players" id="players_button">
        <input type="text" id="datepicker" name="date"  class="market-select">
        <input type="submit" value="Upload Image" name="submit_players">
    </form>

    <form action="loadfile.php" method="post" enctype="multipart/form-data">
    Select file  votes upload:
        <input type="file" name="votes" id="votes_button">
        <input type="number" name="round" min="1" max="38" size="2" class="market-select">
        <input type="submit" value="Upload Image" name="submit_votes">
    </form>

    <?php }else {
        echo " non hai le autorizzazioni";
    }

    }else{
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    } ?>

<?php include('footer.php'); ?>