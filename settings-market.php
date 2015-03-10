<?php
include('header.php');


    if(isset($_SESSION['username'])){
        
        $username=$_SESSION['username'];
        $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);

        $user=$database->getUserByUsername($username);

        $config=$database->dumpConfig();
        
        if($user->getAuth()==1){ 
        
        if(isset($_GET['delete'])){
            $id=$_GET['delete'];
            $database->deleteMarket($id);
        }else if(isset($_POST['start_date']) && isset($_POST['finish_date']) && isset($_POST['max_change']) && isset($_POST['name'])){
            $start_date=$_POST['start_date'];
            $finish_date=$_POST['finish_date'];
            $max_change=$_POST['max_change'];
            $name=$_POST['name'];
            if(isset($_POST['id'])){
                $id=$_POST['id'];
                $database->editMarket($id,$name,$max_change,$start_date,$finish_date);
            }else{
                $database->createMarket($name,$max_change,$start_date,$finish_date);
            }
        }
        
        
        

        if(isset($_GET['edit'])){
            $edit=$_GET['edit'];
            $market=$database->getMarketById($edit);
?>
            
        <div class="main">
            <form action="settings-market.php" method="post">
                <input type="hidden" name="id" <?php echo "value=\"".$market->getId()."\" "; ?> >
                <div class="setting_item">
                    <div class="setting_item_name">Modifica Mercato</div>
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Nome Mercato</div>
                    <input class="setting_item_input" type="text" name="name" class="market-select" <?php echo "value=\"".$market->getName()."\" "; ?> >
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Data Inizio</div>
                    <input class="setting_item_input" type="datetime" id="datetimepicker" name="start_date" class="market-select" <?php echo "value=\"".$market->getStartDate()->format("d-m-Y H:i")."\" "; ?>>
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Data Fine</div>
                    <input class="setting_item_input" type="datetime" id="datetimepicker_finish" name="finish_date" class="market-select" <?php echo "value=\"".$market->getFinishDate()->format("d-m-Y H:i")."\" "; ?>>
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Numero Massimo di Cambi</div>
                    <input class="setting_item_input" size="2" type="text" name="max_change" <?php echo "value=\"".$market->getMaxChange()."\" "; ?> ></input>
                </div>
                <div class="setting_item">
                    <input class="setting_item_input" type="submit" value="Modifica" ></input>
                </div>
            </form>
        </div>

        <?php
        }else{
        ?>
        <div class="main">
            <?php 
            $markets=$database->getMarkets();
            foreach($markets as $market){ ?>
                <div class="setting_item">
                    <div class="setting_item_name"><?php echo $market->getName(); ?></div>
                    <div class="setting_item_descript">Dal <?php echo $market->getStartDate()->format("d/m/Y - H:i"); ?> Fino al <?php echo $market->getFinishDate()->format("d/m/Y - H:i"); ?>
                    ---- <?php echo $market->getMaxChange();?> Cambi</div>
                    <form action="settings-market.php" method="get">
                        <input class="setting_item_input" type="submit" value="Modifica" ></input>
                        <input type="hidden" name="edit" <?php echo "value=\"".$market->getId()."\""; ?> >
                    </form>
                    <form  action="settings-market.php" method="get">
                        <input class="setting_item_input" type="submit" value="Cancella" ></input>
                        <input type="hidden" name="delete" <?php echo "value=\"".$market->getId()."\""; ?> >
                    </form>
                </div>
            <?php } ?>
        </div>
            
        <div class="main">
            <form action="settings-market.php" method="post">
                <div class="setting_item">
                    <div class="setting_item_name">Crea un nuovo Mercato</div>
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Nome Mercato</div>
                    <input class="setting_item_input" type="text" name="name" class="market-select">
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Data Inizio</div>
                    <input class="setting_item_input" type="datetime" id="datetimepicker" name="start_date" class="market-select">
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Data Fine</div>
                    <input class="setting_item_input" type="datetime" id="datetimepicker_finish" name="finish_date" class="market-select">
                </div>
                <div class="setting_item">
                    <div class="setting_item_descript">Numero Massimo di Cambi</div>
                    <input class="setting_item_input" size="2" type="text" name="max_change" ></input>
                </div>
                <div class="setting_item">
                    <input class="setting_item_input" type="submit" value="Crea" ></input>
                </div>
            </form>
        </div>
        <?php }
        }

    }else{
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    }

?>


<?php include('footer.php'); ?>