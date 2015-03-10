<?php
$title="Impostazioni";
include('header.php');


    if(isset($_SESSION['username'])){
        $username=$_SESSION['username'];
        $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);

        if(isset($_POST['tactics'])){
            $value=$_POST['tactics'];
            $name="available-tactics";
            $database->editConfig($name,$value);
        }
        
        if(isset($_POST['max-role-reserve'])){
            $value=$_POST['max-role-reserve'];
            $name="max-role-reserve";
            $database->editConfig($name,$value);
        }

        if(isset($_POST['max_def'])){
            $value=$_POST['max_def'];
            $name="max_def";
            $database->editConfig($name,$value);
        }
        
        if(isset($_POST['max_cen'])){
            $value=$_POST['max_cen'];
            $name="max_cen";
            $database->editConfig($name,$value);
        }
        
        if(isset($_POST['max_att'])){
            $value=$_POST['max_att'];
            $name="max_att";
            $database->editConfig($name,$value);
        }
        
        if(isset($_POST['max_sub'])){
            $value=$_POST['max_sub'];
            $name="max_sub";
            $database->editConfig($name,$value);
        }
        
        if(isset($_POST['default_competition'])){
            $value=$_POST['default_competition'];
            $name="default_competition";
            $database->editConfig($name,$value);
        }


        $user=$database->getUserByUsername($username);

        $config=$database->dumpConfig();
        

        if($user->getAuth()==1){ ?>
        <div class="main">
            <a href="settings-competitions.php">Impostazioni Competizioni</a>
            <a href="settings-market.php">Impostazioni Mercati</a>
        </div>

        <div class="main">
            <form class="settings" action="settings.php" method="post">
                <div class="setting_item">
                    <div class="setting_item_name">Tattiche Disponibili</div>
                    <div class="setting_item_descript">Esempio:343;442 etc.</div>
                    <input class="setting_item_input" size="30" type="text" name="tactics" <?php echo "value=\"".$config['available-tactics']."\""; ?> >
                </div>
                
                <div class="setting_item">
                    <div class="setting_item_name">Numero Difensori</div>
                    <div class="setting_item_descript">Numero dei difensori in rosa</div>
                    <input class="setting_item_input" maxlength="2" size="2" type="text" name="max_def" <?php echo "value=\"".$config['max_def']."\""; ?> >
                </div>

                <div class="setting_item">
                    <div class="setting_item_name">Numero Centrocampisti</div>
                    <div class="setting_item_descript">Numero dei centrocampisti in rosa</div>
                    <input class="setting_item_input" maxlength="2" size="2" type="text" name="max_cen" <?php echo "value=\"".$config['max_cen']."\""; ?> >
                </div>

                <div class="setting_item">
                    <div class="setting_item_name">Numero Attaccanti</div>
                    <div class="setting_item_descript">Numero degli attaccanti in rosa</div>
                    <input class="setting_item_input" maxlength="2" size="2" type="text" name="max_att" <?php echo "value=\"".$config['max_att']."\""; ?> >
                </div>

                <div class="setting_item">
                    <div class="setting_item_name">Numero Panchinari</div>
                    <div class="setting_item_descript">Numero dei giocatori in panchina tranne portieri</div>
                    <input class="setting_item_input" maxlength="2" size="2" type="text" name="max-role-reserve" <?php echo "value=\"".$config['max-role-reserve']."\""; ?> >
                </div>

                <div class="setting_item">
                    <div class="setting_item_name">Numero Sostituzioni</div>
                    <div class="setting_item_descript">Numero massimo di sostituzioni in una giornata</div>
                    <input class="setting_item_input" maxlength="2" size="2" type="text" name="max_sub" <?php echo "value=\"".$config['max_sub']."\""; ?> >
                </div>

                <div class="setting_item">
                    <div class="setting_item_name">Competizione di Default</div>
                    <div class="setting_item_descript">La Competizione principale della Lega</div>
                    <?php $competitions=$database->getCompetitions(); $comp=$database->getCompetition($config['default_competition']); ?>
                    <select class="setting_item_input"  name="default_competition" >
                        <?php foreach($competitions as $competition){ ?>
                            <option <?php echo "value=\"".$competition->getId()."\""; if($comp!=null && $competition->getId()==$comp->getId()) echo " selected"; ?> ><?php echo $competition->getName(); ?></option> 
                        <?php } ?>
                    </select>
                </div>

            <input id="save" type="submit" value="Modifica" />
            </form>
        </div>
        <?php }


    }else{
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    }

?>

<?php include('footer.php'); ?>