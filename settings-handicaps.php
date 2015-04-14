<?php
$title="Impostazioni Handicap";
include('header.php');


if(isset($_SESSION['username'])){
    
    $username=$_SESSION['username'];

    $user=$database_users->getUserByUsername($username);

    $config=$database->dumpConfig();
    
    if($user->getAuth()==1){ 
    
        if(isset($_POST['delete-handicap-round'])){
            $round=$_POST['delete-handicap-round'];
            $database_handicaps->deleteHandicapRound($round);
        }

        if(isset($_POST['delete-handicap-competition'])){
            $id_competition=$_POST['delete-handicap-competition'];
            $database_handicaps->deleteHandicapCompetition($id_competition);
        }

        if(isset($_POST['competitions-form']) && isset($_POST['team']) && isset($_POST['competition']) && isset($_POST['points']) && isset($_POST['description'])){
            $team=$_POST['team'];
            $competition=$_POST['competition'];
            $points=$_POST['points'];
            $description=$_POST['description'];

            $database_handicaps->setHandicapCompetition($team,$competition,$description,$points);
        }

        if(isset($_POST['rounds-form']) && isset($_POST['team']) && isset($_POST['round']) && isset($_POST['points']) && isset($_POST['description'])){
            $team=$_POST['team'];
            $round=$_POST['round'];
            $points=$_POST['points'];
            $description=$_POST['description'];

            $database_handicaps->setHandicapRound($team,$round,$description,$points);
        }
        
        ?>

        <div class="main">
            <div class="container-fluid">
                <?php 
                $handicaps_competitions=$database_handicaps->getHandicapsCompetitions();
                $id=1;
                foreach($handicaps_competitions as $handicap){ 

                    if($handicap->getCompetition()->getId()>$id){
                        $id=$handicap->getCompetition()->getId();
                    ?>
                        <div class="row">
                            <label class="col-md-12"><?php echo $handicap->getCompetition()->getName();?></label>
                        </div>
                    <?php
                        }
                    ?>

                    <div class="row">

                        <form class="form-horizontal" action="settings-handicaps.php" method="post">

                            <label class="col-md-6 " style="font-weight: 300;">
                                <?php echo $handicap->getUser()->getNameTeam(); ?> - <?php echo $handicap->getDescription(); ?> : <?php echo $handicap->getPoints(); ?> Punti
                            </label>

                            <div class="form-group">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-default col-md-12">Cancella</button>
                                </div>
                            </div>

                            <input type="hidden" name="delete-handicap-competition" <?php echo "value=\"".$handicap->getId()."\""; ?> >
                        </form>

                    </div>
                <?php } ?>
            </div>
        </div>
            
        <div class="main">
            <div class="container-fluid">
                <?php 
                $handicaps_rounds=$database_handicaps->getHandicapsRounds();
                $round=1;
                foreach($handicaps_rounds as $handicap){ 

                    if($handicap->getRound()>$round){
                        $round=$handicap->getRound();
                    ?>
                        <div class="row">
                            <label class="col-md-12">Giornata <?php echo $round;?></label>
                        </div>
                    <?php
                        }
                    ?>

                    <div class="row">

                        <form class="form-horizontal" action="settings-handicaps.php" method="post">

                            <label class="col-md-6" style="font-weight: 300;">
                                <?php echo $handicap->getUser()->getNameTeam(); ?> - <?php echo $handicap->getDescription(); ?> : <?php echo $handicap->getPoints(); ?> Punti
                            </label>

                            <div class="form-group">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-default col-md-12">Cancella</button>
                                </div>
                            </div>

                            <input type="hidden" name="delete-handicap-round" <?php echo "value=\"".$handicap->getId()."\""; ?> >
                        </form>

                    </div>
                <?php } ?>
            </div>
        </div>
            
        <div class="main">
            <form class="form-horizontal" action="settings-handicaps.php" method="post">
                
                <div class="form-group">
                    <label class="col-md-12">Crea Handicap Competizione</label>
                </div>

                <div class="form-group">
                    <label class="col-md-4">Competizione</label>
                    <div class="col-md-8">
                        <?php $competitions=$database_competitions->getCompetitions(); $comp=$database_competitions->getCompetition($config['default_competition']); ?>
                        <select class="form-control"  name="competition" >
                            <?php foreach($competitions as $competition){ ?>
                                <option <?php echo "value=\"".$competition->getId()."\""; if($comp!=null && $competition->getId()==$comp->getId()) echo " selected"; ?> ><?php echo $competition->getName(); ?></option> 
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4">Squadra</label>
                    <div class="col-md-8">
                        <?php
                            $users=$database_users->getUsers(); 
                        ?>
                        <select class="form-control"  name="team" >
                            <?php foreach($users as $team){ ?>
                                <option <?php echo "value=\"".$team->getId()."\""; ?> ><?php echo $team->getNameTeam(); ?></option> 
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

        <div class="main">
            <form class="form-horizontal" action="settings-handicaps.php" method="post">
                
                <div class="form-group">
                    <label class="col-md-12">Crea Handicap Giornata</label>
                </div>

                <div class="form-group">
                    <label class="col-md-4">Giornata</label>
                    <div class="col-md-8">
                        <?php $rounds=$database_rounds->getRounds(); ?>
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
                        <?php
                            $users=$database_users->getUsers(); 
                        ?>
                        <select class="form-control"  name="team" >
                            <?php foreach($users as $team){ ?>
                                <option <?php echo "value=\"".$team->getId()."\""; ?> ><?php echo $team->getNameTeam(); ?></option> 
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

        <?php 

    }
    

}else{
    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");
}

?>


<?php include('footer.php'); ?>