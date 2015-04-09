<?php

$title="Inserisci Formazione";
include('header.php');

if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['ids']) && isset($_POST['reserves'])){

    $user=$database_users->getUserByUsername($_SESSION['username']);
    $players=$database_players->dumpSingoliToList(null,null);

    $ids_string=$_POST['ids'];
    $reserves_string=$_POST['reserves'];

    $round=intval($_POST['round']);
    $tactic=$_POST['tactic'];
    
    $ret=$database_rounds->insertTeam($user->getId(),$ids_string,$reserves_string,$round,$tactic);

}

if(!isset($_SESSION['username'])) {

    $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
    header("Location:login.php");

}else if(isset($_SESSION['username'])){

    $username=$_SESSION['username'];
    $user=$database_users->getUserByUsername($username);
    $players=$database_players->dumpSingoliToList(null, null);

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
    

    $team=$database_rounds->getTeam($user->getId(),$round);
    $tactic=$database_rounds->getTactic($user,$round );

    $rescued_team=false;

    if($tactic==null && $round!=1){
        $team=$database_rounds->getTeam($user->getId(),$round-1);
        $tactic=$database_rounds->getTactic($user,$round-1 );
        $rescued_team=true;
    }

    $max_por=1;
    $max_def=4;
    $max_cen=4;
    $max_att=2;

    if($tactic!=null){
        $max_def=$tactic[0];
        $max_cen=$tactic[1];
        $max_att=$tactic[2];
    }

    $num_giocatori=$max_por+$max_def+$max_cen+$max_att;


    $max_role_reserve=2;

    if(isset($config['max-role-reserve'])){
        $max_role_reserve=intval($config["max-role-reserve"]);
    }

    $official_players=($max_role_reserve*3)+1+$num_giocatori;

    if($database_rounds->isPossibleToEditFormation($round)){


    ?>
    <div id="official_players" <?php echo "number=\"".$official_players."\""; ?> ></div>

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
                        <div class="old-player in-roster-player" 
                            <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                            <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                            <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?>
                            <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  
                            <?php if($team->getPlayers()!=null) {  if($team->getPlayers()->searchID($player->getPlayer()->getId())) { echo "style=\"display:none;\" "; } } ?>
                        >

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
                        <div class="old-player in-roster-player" 
                            <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                            <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                            <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?>
                            <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>   
                            <?php if($team->getPlayers()!=null) {  if($team->getPlayers()->searchID($player->getPlayer()->getId())) { echo "style=\"display:none;\" "; } } ?>
                        >

                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                            <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                            <div class="info-player-item">
                                <div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                            </div>
                        </div>
                       <?php }
                        } ?>

                    </div>

                    <div class="roster-item in-roster-player" id="C_free" <?php echo "max=\"".$max_cen."\""; ?>>

                        <?php foreach($roster as $player){

                            if(strtolower($player->getPlayer()->getRole())=="c"){

                        ?>
                        <div class="old-player in-roster-player" 
                            <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                            <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                            <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?>
                            <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>   
                            <?php if($team->getPlayers()!=null) {  if($team->getPlayers()->searchID($player->getPlayer()->getId())) { echo "style=\"display:none;\" "; } } ?>
                        >

                            <div class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></div>
                            <div class="name-player-item"><?php echo $player->getPlayer()->getName(); ?></div>
                            <div class="info-player-item">
                                <div class="value-player-item"><?php echo $player->getPlayer()->getValue(); ?></div>
                            </div>
                        </div>
                       <?php }
                        } ?>

                    </div>

                    <div class="roster-item " id="A_free" <?php echo "max=\"".$max_att."\""; ?>>

                        <?php foreach($roster as $player){

                            if(strtolower($player->getPlayer()->getRole())=="a"){

                        ?>
                        <div class="old-player in-roster-player" 
                            <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                            <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?>
                            <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?>
                            <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>   
                            <?php if($team->getPlayers()!=null) {  if($team->getPlayers()->searchID($player->getPlayer()->getId())) { echo "style=\"display:none;\" "; } } ?>
                        >

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


                        <div class="roster-item " id="P_table" <?php echo "max=\"".$max_por."\""; ?>>
                            
                            <div class="old-player p-but"><div class="name-role">Portieri</div></div>

                            <?php 
                            if($team->getPlayers()!=null){

                                foreach($team->getPlayers()->getByRole("p") as $pla){

                                    if($pla->getPosition()==0){ ?>
                                        <?php $player=$pla->getPlayer(); ?>
                                        <div class="old-player in-team-player" position="0"
                                            <?php echo "id=\"".$player->getId()."_team\" "; ?>
                                            <?php echo "name=\"".$player->getName()."\" "; ?> 
                                            role="P"
                                            <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                            <div class="name-player-item"><?php echo $player->getName(); ?></div>
                                        </div>
                            <?php   }
                                }
                            }
                            ?>
                        </div>


                        <div class="roster-item " id="D_table" <?php echo "max=\"".$max_def."\""; ?>>
                            
                            <div class="old-player d-but"><div class="name-role">Difensori</div></div>

                            <?php 
                            if($team->getPlayers()!=null){

                                foreach($team->getPlayers()->getByRole("d") as $pla){

                                    if($pla->getPosition()==0){ ?>
                                        <?php $player=$pla->getPlayer(); ?>
                                        <div class="old-player in-team-player" position="0"
                                            <?php echo "id=\"".$player->getId()."_team\" "; ?>
                                            <?php echo "name=\"".$player->getName()."\" "; ?> 
                                            role="P"
                                            <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                            <div class="name-player-item"><?php echo $player->getName(); ?></div>
                                        </div>
                            <?php   }
                                }
                            }
                            ?>
                        </div>

                        <div class="roster-item " id="C_table" <?php echo "max=\"".$max_cen."\""; ?>>
                            
                            <div class="old-player c-but"><div class="name-role">Centrocampisti</div></div>

                            <?php 
                            if($team->getPlayers()!=null){

                                foreach($team->getPlayers()->getByRole("c") as $pla){

                                    if($pla->getPosition()==0){ ?>
                                        <?php $player=$pla->getPlayer(); ?>
                                        <div class="old-player in-team-player" position="0"
                                            <?php echo "id=\"".$player->getId()."_team\" "; ?>
                                            <?php echo "name=\"".$player->getName()."\" "; ?> 
                                            role="P"
                                            <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                            <div class="name-player-item"><?php echo $player->getName(); ?></div>
                                        </div>
                            <?php   }
                                }
                            }
                            ?>
                        </div>

                        <div class="roster-item " id="A_table" <?php echo "max=\"".$max_att."\""; ?>>
                            
                            <div class="old-player a-but"><div class="name-role">Attaccanti</div></div>

                            <?php 
                            if($team->getPlayers()!=null){

                                foreach($team->getPlayers()->getByRole("a") as $pla){

                                    if($pla->getPosition()==0){ ?>
                                        <?php $player=$pla->getPlayer(); ?>
                                        <div class="old-player in-team-player" position="0"
                                            <?php echo "id=\"".$player->getId()."_team\" "; ?>
                                            <?php echo "name=\"".$player->getName()."\" "; ?> 
                                            role="P"
                                            <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                                            <div class="name-player-item"><?php echo $player->getName(); ?></div>
                                        </div>
                            <?php   }
                                }
                            }
                            ?>
                        </div>

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

        var len=def_tab.getElementsByClassName("in-team-player").length;
        console.log(len);

        if(len>def){
            var diff=len-def;
            def=parseInt(def);
            console.log("diff:"+diff);

            for(var i=0;i<diff;i++){
                var obj=def_tab.getElementsByClassName("in-team-player")[def];
                console.log(obj);
                var roster_table=document.getElementById(obj.getAttribute("role"));
                var id=obj.getAttribute("id_player");
                var id_element=id;
                var table_element=document.getElementById(id_element);
                table_element.style.display="block";

                document.getElementById(obj.id).remove();
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
        var roster_table=document.getElementById(obj.getAttribute("role")+"_free");
        console.log(roster_table);
        var id=obj.getAttribute("id_player");
        console.log(id);
        var id_element=id;
        console.log(id_element);
        var table_element=document.getElementById(id_element);
        console.log(table_element);
        table_element.style.display="block";
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
        var id_element=id;
        console.log(id_element);
        var table_element=document.getElementById(id_element);
        console.log(table_element);
        table_element.style.display="block";
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

        var max_team=parseInt(table.getAttribute("max"));
        var lenght_table=table.getElementsByClassName("in-team-player").length;

        console.log(table);
        console.log(max_team);
        console.log(lenght_table);

        if(lenght_table<max_team){

            var row = document.createElement('div');

            row.className = "old-player in-team-player";
            row.id = obj.getAttribute("id")+"_team";

            row.setAttribute("name", obj.getAttribute("name"));
            row.setAttribute("role",obj.getAttribute("role"));
            row.setAttribute("id_player",obj.getAttribute("id"));
            row.setAttribute("position","0");

            var namecell = document.createElement('div');
            namecell.innerHTML = obj.getAttribute("name");
            namecell.className = "name-player-item";
            
            row.appendChild(namecell);
            table.appendChild(row);
            console.log(row);

            obj.style.display="none";

        }else{

            var table_reserve=document.getElementById(obj.getAttribute("role")+"_reserve");
            console.log(table_reserve);

            var max_reserve=parseInt(table_reserve.getAttribute("max"));

            var lenght_table_reserve=table_reserve.getElementsByClassName("in-reserve-player").length;

            console.log(table_reserve);
            console.log(max_reserve);
            console.log(lenght_table_reserve);

            if(lenght_table_reserve<max_reserve){

                var row = document.createElement('div');

                row.className = "old-player in-reserve-player";
                row.id = obj.getAttribute("id")+"_reserve";

                row.setAttribute("name", obj.getAttribute("name"));
                row.setAttribute("role",obj.getAttribute("role"));
                row.setAttribute("id_player",obj.getAttribute("id"));
                row.setAttribute("position","1");

                var namecell = document.createElement('div');
                namecell.innerHTML = obj.getAttribute("name");
                namecell.className = "name-player-item";
                
                row.appendChild(namecell);
                table_reserve.appendChild(row);
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
        for (var r = 0, n = table.getElementsByClassName("in-team-player").length; r < n; r++) {
            var item = table.getElementsByClassName("in-team-player")[r].getAttribute("id_player");
            jsonObj.push(item);
        };

        var table = document.getElementById("D_table");
        for (var r = 0, n = table.getElementsByClassName("in-team-player").length; r < n; r++) {
            var item = table.getElementsByClassName("in-team-player")[r].getAttribute("id_player");
            jsonObj.push(item);
        };

        var table = document.getElementById("C_table");
        for (var r = 0, n = table.getElementsByClassName("in-team-player").length; r < n; r++) {
            var item = table.getElementsByClassName("in-team-player")[r].getAttribute("id_player");
            jsonObj.push(item);
        };

        var table = document.getElementById("A_table");
        for (var r = 0, n = table.getElementsByClassName("in-team-player").length; r < n; r++) {
            var item = table.getElementsByClassName("in-team-player")[r].getAttribute("id_player");
            jsonObj.push(item);
        };



        var table = document.getElementById("P_reserve");
        for (var r = 0, n = table.getElementsByClassName("in-reserve-player").length; r < n; r++) {
            var item = table.getElementsByClassName("in-reserve-player")[r].getAttribute("id_player");
            reserves.push(item);
        };

        var table = document.getElementById("D_reserve");
        for (var r = 0, n = table.getElementsByClassName("in-reserve-player").length; r < n; r++) {
            var item = table.getElementsByClassName("in-reserve-player")[r].getAttribute("id_player");
            reserves.push(item);
        };

        var table = document.getElementById("C_reserve");
        for (var r = 0, n = table.getElementsByClassName("in-reserve-player").length; r < n; r++) {
            var item = table.getElementsByClassName("in-reserve-player")[r].getAttribute("id_player");
            reserves.push(item);
        };

        var table = document.getElementById("A_reserve");
        for (var r = 0, n = table.getElementsByClassName("in-reserve-player").length; r < n; r++) {
            var item = table.getElementsByClassName("in-reserve-player")[r].getAttribute("id_player");
            reserves.push(item);
        };

        var tot=reserves.length+jsonObj.length;
        var official=document.getElementById("official_players").getAttribute("number");

        console.log(official);
        console.log(tot);

        if(tot==official){


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

        }


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