<?php
$title="Impostazioni";
include('header.php');


    if($username!=null && $userAuth==1){

        $editConfig=array();

        if(isset($_POST['tactics'])){
            $value=$_POST['tactics'];
            $name="available-tactics";
            $editConfig[]=array(
                "name"=>$name,
                "value"=>$value
            );
        }
        
        if(isset($_POST['max-role-reserve'])){
            $value=$_POST['max-role-reserve'];
            $name="max-role-reserve";
            $editConfig[]=array(
                "name"=>$name,
                "value"=>$value
            );
        }

        if(isset($_POST['max_por'])){
            $value=$_POST['max_por'];
            $name="max_por";
            $editConfig[]=array(
                "name"=>$name,
                "value"=>$value
            );
        }

        if(isset($_POST['max_def'])){
            $value=$_POST['max_def'];
            $name="max_def";
            $editConfig[]=array(
                "name"=>$name,
                "value"=>$value
            );
        }
        
        if(isset($_POST['max_cen'])){
            $value=$_POST['max_cen'];
            $name="max_cen";
            $editConfig[]=array(
                "name"=>$name,
                "value"=>$value
            );
        }
        
        if(isset($_POST['max_att'])){
            $value=$_POST['max_att'];
            $name="max_att";
            $editConfig[]=array(
                "name"=>$name,
                "value"=>$value
            );
        }
        
        if(isset($_POST['max_sub'])){
            $value=$_POST['max_sub'];
            $name="max_sub";
            $editConfig[]=array(
                "name"=>$name,
                "value"=>$value
            );
        }
        
        if(isset($_POST['default_competition'])){
            $value=$_POST['default_competition'];
            $name="default_competition";
            $editConfig[]=array(
                "name"=>$name,
                "value"=>$value
            );
        }
        
        $config=null;
        
        if(count($editConfig)){
	    	$result=$apiAccess->accessApi("/config","POST",array("postParams" => $editConfig));
	    }else{
		    $result=$apiAccess->accessApi("/config","GET");
		}
		
		if($result["error"]==false){
			$config=$result["data"];
		}
        

         ?>
        <div class="main">
            <a href="settings-competitions.php">Impostazioni Competizioni</a>
            <a href="settings-market.php">Impostazioni Mercati</a>
            <a href="settings-handicaps.php">Impostazioni Handicap</a>
        </div>

        <div class="main">
            <form class="form-horizontal" action="settings.php" method="post">
                <div class="form-group">
                    <h3 class="col-md-8 control-label left-label">Tattiche Disponibili&nbsp;&nbsp;<small>Esempio:343;442 etc.</small></h3>
                    <div class="col-md-4">
                        <input class="form-control" size="30" type="text" name="tactics" <?php echo "value=\"".$config['available-tactics']."\""; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <h3 class="col-md-8 control-label left-label">Numero Portieri&nbsp;&nbsp;<small>Numero dei portieri in rosa</small></h3>
                    <div class="col-md-4">
                        <input class="form-control" maxlength="2" size="2" type="text" name="max_por" <?php echo "value=\"".$config['max_por']."\""; ?> >
                    </div>
                </div>
                
                <div class="form-group">
                    <h3 class="col-md-8 control-label left-label">Numero Difensori&nbsp;&nbsp;<small>Numero dei difensori in rosa</small></h3>
                    <div class="col-md-4">
                        <input class="form-control" maxlength="2" size="2" type="text" name="max_def" <?php echo "value=\"".$config['max_def']."\""; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <h3 class="col-md-8 control-label left-label">Numero Centrocampisti&nbsp;&nbsp;<small>Numero dei centrocampisti in rosa</small></h3>
                    <div class="col-md-4">
                        <input class="form-control" maxlength="2" size="2" type="text" name="max_cen" <?php echo "value=\"".$config['max_cen']."\""; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <h3 class="col-md-8 control-label left-label">Numero Attaccanti&nbsp;&nbsp;<small>Numero dei attaccanti in rosa</small></h3>
                    <div class="col-md-4">
                        <input class="form-control" maxlength="2" size="2" type="text" name="max_att" <?php echo "value=\"".$config['max_att']."\""; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <h3 class="col-md-8 control-label left-label">Numero Panchinari&nbsp;&nbsp;<small>Numero dei panchinari per ogni partita (esclusi portieri)</small></h3>
                    <div class="col-md-4">
                        <input class="form-control" maxlength="2" size="2" type="text" name="max-role-reserve" <?php echo "value=\"".$config['max-role-reserve']."\""; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <h3 class="col-md-8 control-label left-label">Numero Sostituzioni&nbsp;&nbsp;<small>Numero massimo di sostituzioni in una giornata</small></h3>
                    <div class="col-md-4">
                        <input class="form-control" maxlength="2" size="2" type="text" name="max_sub" <?php echo "value=\"".$config['max_sub']."\""; ?> >
                    </div>
                </div>

                <div class="form-group">
                    <h3 class="col-md-8 control-label left-label">Competizione di Default&nbsp;&nbsp;<small>La Competizione principale della Lega</small></h3>
                    <div class="col-md-4">
                        <?php $competitions=$database_competitions->getCompetitions(); $comp=$database_competitions->getCompetition($config['default_competition']); ?>
                        <select class="form-control"  name="default_competition" >
                            <?php foreach($competitions as $competition){ ?>
                                <option <?php echo "value=\"".$competition->getId()."\""; if($comp!=null && $competition->getId()==$comp->getId()) echo " selected"; ?> ><?php echo $competition->getName(); ?></option> 
                            <?php } ?>
                        </select>
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
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    }

?>

<?php include('footer.php'); ?>