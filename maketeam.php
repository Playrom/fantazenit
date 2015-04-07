<?php
$title="Inserisci Formazione";
include('header.php');
?>

<?php

    if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['ids']) && isset($_POST['reserves'])){
        $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
        $user=$database->getUserByUsername($_SESSION['username']);

        $players=$database->dumpSingoliToList(null,null);
        $ids_string=$_POST['ids'];
        $reserves_string=$_POST['reserves'];

        $round=intval($_POST['round']);
        $tactic=$_POST['tactic'];
        

        $ret=$database->insertTeam($user->getId(),$ids_string,$reserves_string,$round,$tactic);
        $database->close();
    }

    if(!isset($_SESSION['username'])) {
        $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
        header("Location:login.php");
    }else if(isset($_SESSION['username'])){
        $username=$_SESSION['username'];
        $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
        $user=$database->getUserByUsername($username);
        $players=$database->dumpSingoliToList(null, null);
        $roster=$user->getPlayers();
        $config=$database->dumpConfig();
        $round=1;
        
        $num_giocatori=$config['max_por']+$config['max_def']+$config['max_cen']+$config['max_att'];
        
        if(count($roster)!=$num_giocatori){
            $_SESSION["roster_not_completed"]=true;
            header("Location: createroster.php");
        }

        if(isset($config['current_round'])){
            $round=intval($config['current_round']);
        }
        

        $team=$database->getTeam($user->getId(),$round);
        $tactic=$database->getTactic($user,$round );

        $rescued_team=false;
        

        if($tactic==null && $round!=1){
            $team=$database->getTeam($user->getId(),$round-1);
            $tactic=$database->getTactic($user,$round-1 );
            $rescued_team=true;
        }

        $dif=4;
        $cen=4;
        $att=2;

        if($tactic!=null){
            $dif=$tactic[0];
            $cen=$tactic[1];
            $att=$tactic[2];
        }

        $max_role_reserve=2;

        if(isset($config['max-role-reserve'])){
            $max_role_reserve=intval($config["max-role-reserve"]);
        }

        if($database->isPossibleToEditFormation($round)){
            

    ?>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div id="team-info">
                            <div class="name-team"><?php echo $user->getNameTeam(); ?></div>
                            <div class="balance">Giornata <?php echo $round; ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="side-element col-md-4">
                        <div class="roster-item" id="P_free" <?php echo "max=\"".$max_por."\""; ?> >

                            <?php foreach($roster as $player){
                                if(strtolower($player->getPlayer()->getRole())=="p"){

                            ?>
                              <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                                <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                                <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                                  <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                                  <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                                  <div class="info-player-item">
                                    <div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                                </div>
                              </div>
                           <?php }
                            } ?>

                        </div>

                        <div class="roster-item" id="D_free" <?php echo "max=\"".$max_def."\""; ?>>

                            <?php foreach($roster as $player){

                                if(strtolower($player->getPlayer()->getRole())=="d"){

                            ?>
                            <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                                <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                                <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                                <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                                <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                                <div class="info-player-item">
                                    <div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                                </div>
                            </div>
                           <?php }
                            } ?>

                        </div>

                        <div class="roster-item" id="C_free" <?php echo "max=\"".$max_cen."\""; ?>>

                            <?php foreach($roster as $player){

                                if(strtolower($player->getPlayer()->getRole())=="c"){

                            ?>
                            <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                                <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                                <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                                <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                                <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                                <div class="info-player-item">
                                    <div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                                </div>
                            </div>
                           <?php }
                            } ?>

                        </div>

                        <div class="roster-item" id="A_free" <?php echo "max=\"".$max_att."\""; ?>>

                            <?php foreach($roster as $player){

                                if(strtolower($player->getPlayer()->getRole())=="a"){

                            ?>
                            <div class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                                <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                                <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                                <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                                <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                                <div class="info-player-item">
                                    <div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                                </div>
                            </div>
                           <?php }
                            } ?>

                        </div>
                    </div> <!-- side roster end -->

                    <div class="side-element col-md-8">
                        <div id="utility-row">
                            <select name="module" id="module" <?php echo "round=\"".$round."\""; ?> onchange="changemodule(this)">
                                  <?php
                                  if(isset($config["available-tactics"])){
                                    $modules=explode(";",$config["available-tactics"]);
                                  }
                                  foreach($modules as $module){
                                    if(strtolower($tactic)==strtolower($module)){
                                  ?>
                                    <option selected="selected" <?php echo "value=\"".$module."\""; ?> ><?php echo $module;?></option>
                                  <?php } else { ?>
                                    <option <?php echo "value=\"".$module."\""; ?> ><?php echo $module;?></option>
                                  <?php } } ?>
                            </select>
                            <?php if($rescued_team) { ?><span class="rescued">Formazione Recuperata dal Turno Precedente</span><?php } ?>
                        </div>

                        <div id="save" onclick="getValues();">Salva Formazione</div>

                        <div id="team">

                            <table id="P_table" max="1">
                                <tr class="p-but"><td class="name-role">Portieri</td></tr>
                                <?php
                            if($team->getPlayers()!=null){
                                foreach($team->getPlayers()->getByRole("p") as $pla){
                                    if($pla->getPosition()==0){ ?>
                                    <?php $player=$pla->getPlayer(); ?>
                                    <tr class="in-team-player" position="0" <?php echo "id=\"".$player->getId()."_team\" "; ?>
                                    <?php echo "name=\"".$player->getName()."\" "; ?>  role="P" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                    <td><?php echo $player->getName(); ?></td>
                				</tr>

                                    <?php }

                                }
                                
                            }
                                ?>

                            </table>

                            <table id="D_table" <?php echo "max=\"".$dif."\" "; ?>>
                                <tr class="d-but"><td class="name-role">Difensori</td></tr>
                                <?php
                            if($team->getPlayers()!=null){
                                foreach($team->getPlayers()->getByRole("d") as $pla){
                                    if($pla->getPosition()==0){ ?>
                                    <?php $player=$pla->getPlayer(); ?>
                                    <tr class="in-team-player" position="0" <?php echo "id=\"".$player->getId()."_team\" "; ?>
                                    <?php echo "name=\"".$player->getName()."\" "; ?>  role="D" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                    <td><?php echo $player->getName(); ?></td></tr>

                                    <?php }

                                }
                            }
                                ?>

                            </table>

                            <table id="C_table" <?php echo "max=\"".$cen."\" "; ?>>
                                <tr class="c-but"><td class="name-role">Centrocampisti</td></tr>
                                <?php
                            if($team->getPlayers()!=null){
                                foreach($team->getPlayers()->getByRole("c") as $pla){
                                    if($pla->getPosition()==0){ ?>
                                    <?php $player=$pla->getPlayer(); ?>
                                    <tr class="in-team-player" position="0" <?php echo "id=\"".$player->getId()."_team\" "; ?>
                                    <?php echo "name=\"".$player->getName()."\" "; ?>  role="C" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                    <td><?php echo $player->getName(); ?></td></tr>

                                    <?php }

                                }
                            }
                                ?>

                            </table>

                            <table id="A_table" <?php echo "max=\"".$att."\" "; ?>>
                                <tr class="a-but"><td class="name-role">Attaccanti</td></tr>
                                <?php
                            if($team->getPlayers()!=null){
                                foreach($team->getPlayers()->getByRole("a") as $pla){
                                    if($pla->getPosition()==0){ ?>
                                    <?php $player=$pla->getPlayer(); ?>
                                    <tr class="in-team-player" position="0" <?php echo "id=\"".$player->getId()."_team\" "; ?>
                                    <?php echo "name=\"".$player->getName()."\" "; ?>  role="A" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                    <td><?php echo $player->getName(); ?></td></tr>

                                    <?php }

                                }
                            }
                                ?>

                            </table>

                        </div> <!-- end team -->

                        <div id="reserve_team">
                            <div class="type_name">Panchina</div>

                            <table id="P_reserve" max="1">
                                <tr class="p-but"><td class="name-role">Portieri</td></tr>
                                <?php
                            if($team->getPlayers()!=null){
                                foreach($team->getPlayers()->getByRole("p") as $pla){

                                    if($pla->getPosition()==1){ ?>
                                    <?php $player=$pla->getPlayer(); ?>
                                    <tr class="in-reserve-player"  position="1" <?php echo "id=\"".$player->getId()."_reserve\" "; ?>
                                    <?php echo "name=\"".$player->getName()."\" "; ?> role="P" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                    <td><?php echo $player->getName(); ?></td></tr>
                                    <?php }

                                }
                            }
                                ?>
                            </table>

                            <table id="D_reserve" <?php echo "max=\"".$max_role_reserve."\" "; ?>>
                                <tr class="d-but"><td class="name-role">Difensori</td></tr>
                                <?php
                            if($team->getPlayers()!=null){
                                foreach($team->getPlayers()->getByRole("d") as $pla){

                                    if($pla->getPosition()==1){ ?>
                                    <?php $player=$pla->getPlayer(); ?>
                                    <tr class="in-reserve-player"  position="1" <?php echo "id=\"".$player->getId()."_reserve\" "; ?>
                                    <?php echo "name=\"".$player->getName()."\" "; ?> role="D" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                    <td><?php echo $player->getName(); ?></td></tr>
                                    <?php }

                                }
                            }
                                ?>
                            </table>

                            <table id="C_reserve" <?php echo "max=\"".$max_role_reserve."\" "; ?>>
                                <tr class="c-but"><td class="name-role">Centrocampisti</td></tr>
                                <?php
                            if($team->getPlayers()!=null){
                                foreach($team->getPlayers()->getByRole("c") as $pla){

                                    if($pla->getPosition()==1){ ?>
                                    <?php $player=$pla->getPlayer(); ?>
                                    <tr class="in-reserve-player"  position="1" <?php echo "id=\"".$player->getId()."_reserve\" "; ?>
                                    <?php echo "name=\"".$player->getName()."\" "; ?> role="C" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                    <td><?php echo $player->getName(); ?></td></tr>
                                    <?php }

                                }
                            }
                                ?>
                            </table>

                            <table id="A_reserve" <?php echo "max=\"".$max_role_reserve."\" "; ?>>
                                <tr class="a-but"><td class="name-role">Attaccanti</td></tr>
                                <?php
                            if($team->getPlayers()!=null){
                                foreach($team->getPlayers()->getByRole("a") as $pla){

                                    if($pla->getPosition()==1){ ?>
                                        <?php $player=$pla->getPlayer(); ?>
                                        <tr class="in-reserve-player"  position="1" <?php echo "id=\"".$player->getId()."_reserve\" "; ?>
                                        <?php echo "name=\"".$player->getName()."\" "; ?> role="A" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                        <td><?php echo $player->getName(); ?></td></tr>
                                    <?php }

                                }
                            }
                                ?>
                            </table>

                        </div> <!-- reserve end -->
                    </div>
                </div> <!-- row end -->
            </div> <!-- container end -->
        <?php }else{ ?>
        <div class="alert alert-danger error_display" role="alert">
            <span class="glyphicon glyphicon-alert" aria-hidden="true"></span>
            <span class="sr-only"></span>Attenzione , non è piu possibile inserire la formazione
        </div>
      <?php } ?>
<?php }

?>


<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>
<script>


    var disable_but=function(bo){
        var mod_button=document.getElementById("mod-button");
        mod_button.disabled=bo;
    };

    var changemodule=function(mo){
        console.log(mo);
        var tact=mo.value;
        console.log(tact);
        var def=tact[0];
        var cen=tact[1];
        var att=tact[2];

        var def_tab=document.getElementById("D_table");
        def_tab.setAttribute("max",def);
        in_module_change(def_tab,def);

        var cen_tab=document.getElementById("C_table");
        cen_tab.setAttribute("max",cen);
        in_module_change(cen_tab,cen);


        var att_tab=document.getElementById("A_table");
        att_tab.setAttribute("max",att);
        in_module_change(att_tab,att);

        };

    var in_module_change=function(def_tab,def){
        console.log(def);
        console.log(def_tab.rows.length);
        if(def_tab.rows.length>def){
            var diff=def_tab.rows.length-def-1;
            def=parseInt(def);
            console.log("diff:"+diff);
            for(var i=0;i<diff;i++){
                var obj=def_tab.rows[def];
                console.log(obj);
                var roster_table=document.getElementById(obj.getAttribute("role"));
                var id=obj.getAttribute("id_player");
                var id_element=id+"_roster";
                var table_element=document.getElementById(id_element);
                table_element.style.display="table-row";

                def_tab.deleteRow(def);
            }

        }
    };

    var change = function(obj){
        var min=obj["fromNumber"];
        var max=obj["toNumber"];
        console.log(min);
        console.log(max);
        var players=document.getElementsByClassName("new-player");
        for (var i = 0; i < players.length; ++i) {
            var player = players[i];
            var value=player.getAttribute("data-value");
            if(value<min || value>max){
                player.style.display="none";
            }else{
                player.style.display="table-row";
            }
        }

    };
    var ol=null,ne=null;
    var remove_roster=function(pass){
        var obj=pass.currentTarget;
        console.log(obj);
        var roster_table=document.getElementById(obj.getAttribute("role"));
        console.log(roster_table);
        var id=obj.getAttribute("id_player");
        console.log(id);
        var id_element=id+"_roster";
        console.log(id_element);
        var table_element=document.getElementById(id_element);
        console.log(table_element);
        table_element.style.display="table-row";
        var index=obj.rowIndex;
        console.log(index);

        obj.parentNode.removeChild(obj);

    };

    var remove_reserve=function(pass){
        var obj=pass.currentTarget;
        console.log(obj);
        var roster_table=document.getElementById(obj.getAttribute("role"));
        console.log(roster_table);
        var id=obj.getAttribute("id_player");
        console.log(id);
        var id_element=id+"_roster";
        console.log(id_element);
        var table_element=document.getElementById(id_element);
        console.log(table_element);
        table_element.style.display="table-row";
        var index=obj.rowIndex;
        console.log(index);

        obj.parentNode.removeChild(obj);

    };

    var add_roster=function(pass){
        var obj=pass.currentTarget;


            console.log(obj);
            console.log(obj.getAttribute("role")+"_table");
            var table=document.getElementById(obj.getAttribute("role")+"_table");
            console.log(table);

            var max_team=parseInt(table.getAttribute("max"))+1;
            var lenght_table=table.rows.length;
            console.log(table);
            console.log(max_team);
            console.log(lenght_table);
            if(lenght_table<max_team){
                var row=table.insertRow();
                row.className="in-team-player";
                row.id=obj.getAttribute("id_player")+"_team";
                row.setAttribute("name",obj.getAttribute("name"));
                row.setAttribute("role",obj.getAttribute("role"));
                row.setAttribute("id_player",obj.getAttribute("id_player"));
                row.setAttribute("position","0");
                //row.setAttribute("onclick","javascript:alert(1);");
                var namecell=row.insertCell();
                namecell.innerHTML=obj.getAttribute("name");
                console.log(row);
                obj.style.display="none";
            }else{

                var table_reserve=document.getElementById(obj.getAttribute("role")+"_reserve");
                console.log(table_reserve);

                var max_reserve=parseInt(table_reserve.getAttribute("max"))+1;
                var lenght_table_reserve=table_reserve.rows.length;
                console.log(table_reserve);
                console.log(max_reserve);
                console.log(lenght_table_reserve);
                if(lenght_table_reserve<max_reserve){
                    var row=table_reserve.insertRow();
                    row.className="in-reserve-player";
                    row.id=obj.getAttribute("id_player")+"_reserve";
                    row.setAttribute("name",obj.getAttribute("name"));
                    row.setAttribute("role",obj.getAttribute("role"));
                    row.setAttribute("id_player",obj.getAttribute("id_player"));
                    row.setAttribute("position","1");
                    //row.setAttribute("onclick","javascript:alert(1);");
                    var namecell=row.insertCell();
                    namecell.innerHTML=obj.getAttribute("name");
                    console.log(row);
                    obj.style.display="none";
                };
            };


    };

    var getValues=function(){
        var jsonObj = [];
        var reserves=[];
        var jsonString;
        var table = document.getElementById("P_table");
        for (var r = 1, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            jsonObj.push(item);
        };

        var table = document.getElementById("D_table");
        for (var r = 1, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            jsonObj.push(item);
        };

        var table = document.getElementById("C_table");
        for (var r = 1, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            jsonObj.push(item);
        };

        var table = document.getElementById("A_table");
        for (var r = 1, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            jsonObj.push(item);
        };



        var table = document.getElementById("P_reserve");
        for (var r = 1, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            reserves.push(item);
        };

        var table = document.getElementById("D_reserve");
        for (var r = 1, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            reserves.push(item);
        };

        var table = document.getElementById("C_reserve");
        for (var r = 1, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            reserves.push(item);
        };

        var table = document.getElementById("A_reserve");
        for (var r = 1, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            reserves.push(item);
        };


        jsonString = JSON.stringify(jsonObj);
        console.log(jsonString);

       var url = 'maketeam.php';
       var text='<form action="' + url + '" method="post">';

       for(var i=0, n=jsonObj.length;i<n;i++){
           text=text+'<input type="hidden" name="ids[]" value="'+jsonObj[i]+'" />';
       }

       for(var i=0, n=reserves.length;i<n;i++){
           text=text+'<input type="hidden" name="reserves[]" value="'+reserves[i]+'" />';
       }

       var tactic_form=document.getElementById("module");
       console.log(tactic_form);
       var tactic=tactic_form.options[tactic_form.selectedIndex].value;
       console.log(tactic);

       var round=tactic_form.getAttribute("round");
       text=text+'<input type="hidden" name="round" value="'+round+'" />';
       text=text+'<input type="hidden" name="tactic" value="'+tactic+'" />';
       console.log(text);
        var form = $(text + '</form>');
        console.log(form);

        $('body').append(form);  // This line is not necessary
        $(form).submit();


    };

    var balance=function(){
        var item=document.getElementById("market-cost");
        return parseInt(item.innerHTML);
    };

    /* var cost_change=function(cost,add){
        if(add){
            var item=document.getElementById("market-cost");
            var balance=item.innerHTML;
            item.innerHTML=parseInt(balance)-parseInt(cost);
            var value=parseInt(item.innerHTML);
            if(value<0) disable_but(true); else disable_but(false);

        }else{
           var item=document.getElementById("market-cost");
            var balance=item.innerHTML;
            item.innerHTML=parseInt(balance)+parseInt(cost);
            var value=parseInt(item.innerHTML);
            if(value<0) disable_but(true); else disable_but(false);

        }
    }; */


    $("body").on('click','.in-team-player',remove_roster);
    $("body").on('click','.in-reserve-player',remove_reserve);

    $("body").on('click','.in-roster-player',add_roster);

</script>

<?php include('footer.php'); ?>