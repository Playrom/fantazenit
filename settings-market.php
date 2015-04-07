<?php
$title="Impostazioni Mercato";
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
            <form class="form-horizontal" action="settings-market.php" method="post">
                <input type="hidden" name="id" <?php echo "value=\"".$market->getId()."\" "; ?> >
                
                <div class="form-group">
                    <label class="col-md-12">Modifica Mercato</label>
                </div>

                <div class="form-group">
                    <label class="col-md-4">Nome Competizione</label>
                    <div class=" col-md-8">
                        <input class="form-control" type="text" name="name"  <?php echo "value=\"".$market->getName()."\" "; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4">Data Inizio</label>
                    <div class=" col-md-8">
                        <input class="form-control" type="datetime" id="datetimepicker" name="start_date"  <?php echo "value=\"".$market->getStartDate()->format("d-m-Y H:i")."\" "; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4">Data Fine</label>
                    <div class=" col-md-8">
                        <input class="form-control" type="datetime" id="datetimepicker_finish" name="finish_date"  <?php echo "value=\"".$market->getFinishDate()->format("d-m-Y H:i")."\" "; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4">Numero Massimo di Cambi</label>
                    <div class=" col-md-8">
                        <input class="form-control" size="2" type="text" name="max_change" <?php echo "value=\"".$market->getMaxChange()."\" "; ?> >
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
        ?>
        
        <div class="main">
            <div class="container-fluid">
                <?php 
                $markets=$database->getMarkets();
                foreach($markets as $market){ ?>
                    <div class="row">

                        <div class="col-md-8"><?php echo $market->getName(); ?>
                            <div class="setting_item_descript">Dal <?php echo $market->getStartDate()->format("d/m/Y - H:i"); ?> Fino al <?php echo $market->getFinishDate()->format("d/m/Y - H:i"); ?>
                            ---- <?php echo $market->getMaxChange();?> Cambi</div>
                        </div>

                        <form action="settings-market.php" method="get">

                            <div class="form-group">
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-default col-md-12">Modifica</button>
                                </div>
                            </div>

                            <input type="hidden" name="edit" <?php echo "value=\"".$market->getId()."\""; ?> >

                        </form>

                        <form  action="settings-market.php" method="get">

                            <div class="form-group">
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-default col-md-12">Cancella</button>
                                </div>
                            </div>

                            <input type="hidden" name="delete" <?php echo "value=\"".$market->getId()."\""; ?> >
                        </form>

                    </div>
                <?php } ?>
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
        <?php }
        }

    }else{
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    }

?>


<?php include('footer.php'); ?>