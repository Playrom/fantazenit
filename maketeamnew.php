<?php 
function __autoload($class_name) {
  require_once $class_name . '.php';
}
ob_start();
session_start();

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Change Roster</title>
    <link href="css/ion.rangeSlider.css" rel="stylesheet" />
    <link href="css/normalize.min.css" rel="stylesheet"/>
    <link href="css/ion.rangeSlider.skinFlat.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
       
</head>
<body>

 <div id="wrapper">

    <div id="header">
        
        <div id="logo"></div>
        <div id="menu-top"></div>

    </div>

    <div id="menu-settings"></div>

    <div id="menu-info"></div>

    <div id="content">

    <?php
        
        if(!isset($_SESSION['username'])) {
             header("Location:login.php");
        }else if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='GET'){
            $username=$_SESSION['username'];
                $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
                $user=$database->getUserByUsername($username);

                $players=$database->dumpSingoliToList(null, null);
                $roster=$user->getPlayers();
                $config=$database->dumpConfig();
                $round=1;

                if(isset($config['current_round'])){
                    $round=intval($config['current_round']);
                }

                $team=$database->geatTeam($user,$players,$round);
                $tactic=$database->getTactic($user,$round );

                $dif=$tactic[0];
                $cen=$tactic[1];
                $att=$tactic[2];

                if($tactic==null){

                }

                $max_role_reserve=2;

                if(isset($config['max-role-reserve'])){
                    $max_role_reserve=intval($config["max-role-reserve"]);
                }
                
                $database->close();
        ?>

        <div id="team-info">
            <div id="name-team"><?php echo $user->getNameTeam(); ?></div>
            <div id="balance">Giornata <?php echo $round; ?></div>
        </div>

        <div id="side-roster">
            <table class="roster-item" id="P" max="1">
                <tr class="p-but"><td class="name-role">Portieri</td></tr>
                <?php foreach($roster as $player){ 
                if(strtolower($player->getPlayer()->getRole())=="p"){
                        
                ?>
                <tr <?php if($team->getPlayers()->searchID($player->getPlayer()->getId())) { echo "style=\"display:none;\" "; } ?> class="in-roster-player" 
                 <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?> <?php echo "id=\"".$player->getPlayer()->getId()."_roster\" "; ?>
                 <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> <?php echo "id_player=\"".$player->getPlayer()->getId()."\" "; ?> >
                      <td><?php echo $player->getPlayer()->getName(); ?></td>
                  </tr>  
               <?php } 
                } ?>
            </table>

            <table class="roster-item" id="D" max="3">
                <tr class="d-but"><td class="name-role">Difensori</td></tr>
                <?php foreach($roster as $player){ 
                if(strtolower($player->getPlayer()->getRole())=="d"){
                        
                ?>
                <tr <?php if($team->getPlayers()->searchID($player->getPlayer()->getId())) { echo "style=\"display:none;\" "; } ?> class="in-roster-player" 
                 <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?> <?php echo "id=\"".$player->getPlayer()->getId()."_roster\" "; ?>
                 <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> <?php echo "id_player=\"".$player->getPlayer()->getId()."\" "; ?> >
                      <td><?php echo $player->getPlayer()->getName(); ?></td>
                  </tr>  
               <?php } 
                } ?>
            </table>


            <table class="roster-item" id="C" max="4">
                <tr class="c-but"><td class="name-role">Centrocampisti</td></tr>
                <?php foreach($roster as $player){ 
                if(strtolower($player->getPlayer()->getRole())=="c"){
                        
                ?>
                <tr <?php if($team->getPlayers()->searchID($player->getPlayer()->getId())) { echo "style=\"display:none;\" "; } ?> class="in-roster-player" 
                 <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?> <?php echo "id=\"".$player->getPlayer()->getId()."_roster\" "; ?>
                 <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> <?php echo "id_player=\"".$player->getPlayer()->getId()."\" "; ?> >
                      <td><?php echo $player->getPlayer()->getName(); ?></td>
                  </tr>  
               <?php } 
                } ?>
            </table>


            <table class="roster-item" id="A" max="3">
                <tr class="a-but"><td class="name-role">Attaccanti</td></tr>
                <?php foreach($roster as $player){ 
                if(strtolower($player->getPlayer()->getRole())=="a"){
                        
                ?>
                <tr <?php if($team->getPlayers()->searchID($player->getPlayer()->getId())) { echo "style=\"display:none;\" "; } ?> class="in-roster-player" 
                 <?php echo "role=\"".$player->getPlayer()->getRole()."\" "; ?> <?php echo "id=\"".$player->getPlayer()->getId()."_roster\" "; ?>
                 <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> <?php echo "id_player=\"".$player->getPlayer()->getId()."\" "; ?> >
                      <td><?php echo $player->getPlayer()->getName(); ?></td>
                  </tr>  
               <?php } 
                } ?>
            </table>


        </div>

        <div id="side-players">
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
            </div>

            <div id="save" onclick="getValues();">Salva Formazione</div>

            <div id="team">

                <table id="P_table" max="1">
                    <tr class="p-but"><td class="name-role">Portieri</td></tr>
                    <?php 
                    foreach($team->getPlayers()->getByRole("p") as $pla){
                        if($pla->getPosition()==0){ ?>
                        <?php $player=$pla->getPlayer(); ?>
                        <tr class="in-team-player" position="0" <?php echo "id=\"".$player->getId()."_team\" "; ?> 
                        <?php echo "name=\"".$player->getName()."\" "; ?>  role="P" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                        <td><?php echo $player->getName(); ?></td></tr>  

                        <?php }
                        
                    }
                    ?>
                    
                </table>
                    
                <table id="D_table" <?php echo "max=\"".$dif."\" "; ?>>
                    <tr class="d-but"><td class="name-role">Difensori</td></tr>
                    <?php 
                    foreach($team->getPlayers()->getByRole("d") as $pla){
                        if($pla->getPosition()==0){ ?>
                        <?php $player=$pla->getPlayer(); ?>
                        <tr class="in-team-player" position="0" <?php echo "id=\"".$player->getId()."_team\" "; ?> 
                        <?php echo "name=\"".$player->getName()."\" "; ?>  role="D" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                        <td><?php echo $player->getName(); ?></td></tr>  

                        <?php }
                        
                    }
                    ?>
                    
                </table>
                    
                <table id="C_table" <?php echo "max=\"".$cen."\" "; ?>>
                    <tr class="c-but"><td class="name-role">Centrocampisti</td></tr>
                    <?php 
                    foreach($team->getPlayers()->getByRole("c") as $pla){
                        if($pla->getPosition()==0){ ?>
                        <?php $player=$pla->getPlayer(); ?>
                        <tr class="in-team-player" position="0" <?php echo "id=\"".$player->getId()."_team\" "; ?> 
                        <?php echo "name=\"".$player->getName()."\" "; ?>  role="C" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                        <td><?php echo $player->getName(); ?></td></tr>  

                        <?php }
                        
                    }
                    ?>
                    
                </table>
                    
                <table id="A_table" <?php echo "max=\"".$att."\" "; ?>>
                    <tr class="a-but"><td class="name-role">Attaccanti</td></tr>
                    <?php 
                    foreach($team->getPlayers()->getByRole("a") as $pla){
                        if($pla->getPosition()==0){ ?>
                        <?php $player=$pla->getPlayer(); ?>
                        <tr class="in-team-player" position="0" <?php echo "id=\"".$player->getId()."_team\" "; ?> 
                        <?php echo "name=\"".$player->getName()."\" "; ?>  role="A" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                        <td><?php echo $player->getName(); ?></td></tr>  

                        <?php }
                        
                    }
                    ?>
                    
                </table>

            </div>

            <div id="reserve_team">
                <div class="type_name">Panchina</div>

                <table id="P_reserve" max="1">
                    <tr class="p-but"><td class="name-role">Portieri</td></tr>
                    <?php 
                    foreach($team->getPlayers()->getByRole("p") as $pla){

                        if($pla->getPosition()==1){ ?>
                        <?php $player=$pla->getPlayer(); ?>
                        <tr class="in-reserve-player"  position="1" <?php echo "id=\"".$player->getId()."_reserve\" "; ?>
                        <?php echo "name=\"".$player->getName()."\" "; ?> role="P" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                        <td><?php echo $player->getName(); ?></td></tr>  
                        <?php }
                        
                    }
                    ?>
                </table>
                    
                <table id="D_reserve" <?php echo "max=\"".$max_role_reserve."\" "; ?>>
                    <tr class="d-but"><td class="name-role">Difensori</td></tr>
                    <?php 
                    foreach($team->getPlayers()->getByRole("d") as $pla){

                        if($pla->getPosition()==1){ ?>
                        <?php $player=$pla->getPlayer(); ?>
                        <tr class="in-reserve-player"  position="1" <?php echo "id=\"".$player->getId()."_reserve\" "; ?>
                        <?php echo "name=\"".$player->getName()."\" "; ?> role="D" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                        <td><?php echo $player->getName(); ?></td></tr>  
                        <?php }
                        
                    }
                    ?>
                </table>
                    
                <table id="C_reserve" <?php echo "max=\"".$max_role_reserve."\" "; ?>>
                    <tr class="c-but"><td class="name-role">Centrocampisti</td></tr>
                    <?php 
                    foreach($team->getPlayers()->getByRole("c") as $pla){

                        if($pla->getPosition()==1){ ?>
                        <?php $player=$pla->getPlayer(); ?>
                        <tr class="in-reserve-player"  position="1" <?php echo "id=\"".$player->getId()."_reserve\" "; ?>
                        <?php echo "name=\"".$player->getName()."\" "; ?> role="C" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                        <td><?php echo $player->getName(); ?></td></tr>  
                        <?php }
                        
                    }
                    ?>
                </table>
                    
                <table id="A_reserve" <?php echo "max=\"".$max_role_reserve."\" "; ?>>
                    <tr class="a-but"><td class="name-role">Attaccanti</td></tr>
                    <?php 
                    foreach($team->getPlayers()->getByRole("a") as $pla){

                        if($pla->getPosition()==1){ ?>
                            <?php $player=$pla->getPlayer(); ?>
                            <tr class="in-reserve-player"  position="1" <?php echo "id=\"".$player->getId()."_reserve\" "; ?>
                            <?php echo "name=\"".$player->getName()."\" "; ?> role="A" <?php echo "id_player=\"".$player->getId()."\" "; ?> >
                            <td><?php echo $player->getName(); ?></td></tr>  
                        <?php }
                        
                    }
                    ?>
                </table>

            </div>

        </div>
        </div>
    <?php }else if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['ids']) && isset($_POST['reserves'])){
        $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
        $user=$database->getUserByUsername($_SESSION['username']);

        $players=$database->dumpSingoliToList(null,null);
        $ids_string=$_POST['ids'];
        $reserves_string=$_POST['reserves'];

        $round=intval($_POST['round']);
        $tactic=$_POST['tactic'];

        $database->insertTeam($user,$players,$ids_string,$reserves_string,$round,$tactic);

        $database->close();


    }
    
    ?>

    </div>

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
        console.log(def_tab);
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
        if(def_tab.rows.length>def){
            var diff=def_tab.rows.length-def;
            def=parseInt(def);
            console.log("def:"+def);
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
                row.className=obj.getAttribute("role")+" in-team-player";
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
                    row.className=obj.getAttribute("role")+" in-reserve-player";
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
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            jsonObj.push(item);
        };
        
        var table = document.getElementById("D_table");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            jsonObj.push(item);
        };
        
        var table = document.getElementById("C_table");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            jsonObj.push(item);
        };
        
        var table = document.getElementById("A_table");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            jsonObj.push(item);
        };
        
        
        
        var table = document.getElementById("P_reserve");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            reserves.push(item);
        };
        
        var table = document.getElementById("D_reserve");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            reserves.push(item);
        };
        
        var table = document.getElementById("C_reserve");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id_player");
            reserves.push(item);
        };
        
        var table = document.getElementById("A_reserve");
        for (var r = 0, n = table.rows.length; r < n; r++) {
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

</body>