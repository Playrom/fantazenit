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
            $_SESSION['old_url']=$_SERVER['REQUEST_URI'];
            header("Location:login.php");
        }else if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='GET'){
            
                $username=$_SESSION['username'];
                $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
                $user=$database->getUserByUsername($username);

                $players=$database->dumpSingoliToList(null, null);
                $roster=$user->getPlayers();

                $database->close();
        ?>
        <div id="team-info">
            
        <div id="name-team"><?php echo $user->getNameTeam(); ?></div>
        <div id="balance">Soldi Disponibili:<div id="market-cost"><?php echo $user->getBalance(); ?></div></div>

        </div>

        <div id="side-roster">
        

            <table class="roster-item" id="P_free" max="3">

                <?php foreach($roster as $player){ 
                    if(strtolower($player->getPlayer()->getRole())=="p"){

                ?>
                  <tr class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                    <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?> 
                    <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?>  >
                      <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></td>
                      <td><?php echo $player->getPlayer()->getName(); ?></td>
                      <td><?php echo $player->getPlayer()->getValue(); ?></td> 
                  </tr>  
               <?php } 
                } ?>

            </table>

            <table class="roster-item" id="D_free" max="8">

                <?php foreach($roster as $player){ 
                    
                    if(strtolower($player->getPlayer()->getRole())=="d"){

                ?>
                <tr class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                    <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?> 
                    <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                    <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></td>
                    <td><?php echo $player->getPlayer()->getName(); ?></td>
                    <td><?php echo $player->getPlayer()->getValue(); ?></td> 
                </tr>  
               <?php } 
                } ?>

            </table>

            <table class="roster-item" id="C_free" max="8">

                <?php foreach($roster as $player){ 
                    
                    if(strtolower($player->getPlayer()->getRole())=="c"){

                ?>
                <tr class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                    <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?> 
                    <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                    <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></td>
                    <td><?php echo $player->getPlayer()->getName(); ?></td>
                    <td><?php echo $player->getPlayer()->getValue(); ?></td> 
                </tr>  
               <?php } 
                } ?>

            </table>

            <table class="roster-item" id="A_free" max="6">

                <?php foreach($roster as $player){ 
                    
                    if(strtolower($player->getPlayer()->getRole())=="a"){

                ?>
                <tr class="old-player" <?php echo "id=\"".$player->getPlayer()->getId()."\" "; ?>
                    <?php echo "data-value=\"".$player->getPlayer()->getValue()."\" "; ?> 
                    <?php echo "name=\"".$player->getPlayer()->getName()."\" "; ?> >
                    <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getPlayer()->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getPlayer()->getRole()); ?></span></td>
                    <td><?php echo $player->getPlayer()->getName(); ?></td>
                    <td><?php echo $player->getPlayer()->getValue(); ?></td> 
                </tr>  
               <?php } 
                } ?>

            </table>

        </div>

        <div id="side-players">
            <div id="save" onclick="getValues()">Salva Roster</div> 
            <div id="utility-row">
                <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)" onclick="select_role('P',this)"><div class="but-role p-but">P</div><div class="but-over p-but"></div></div>
                <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)" onclick="select_role('D',this)"><div class="but-role d-but">D</div><div class="but-over d-but"></div></div>
                <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)"  onclick="select_role('C',this)"><div class="but-role c-but">C</div><div class="but-over c-but"></div></div>
                <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)"  onclick="select_role('A',this)"><div class="but-role a-but">A</div><div class="but-over a-but"></div></div>
                <div id="value-selector"><input type="text" id="range_1" /></div>
                
            </div> 

            <div id="players">

                <div id="search-box"><input class="search" id="search-element" placeholder="Cerca">
                    <button id="sort-name-button" class="sort" data-sort="name-new-player">Ordina per Nome</button>
                    <button id="sort-value-button" class="sort" data-sort="value-new-player">Ordina per Costo</button>
                </div>

                <table id="free-table">
                    <tbody class="list">
                      <?php  foreach($players as $player){   ?>
                      <tr class="new-player" <?php echo "id=\"".$player->getId()."_free\" "; ?>
                       <?php echo "id_player=\"".$player->getId()."\" "; ?>
                        class="free-player" 
                        <?php echo "data-value=\"".$player->getValue()."\" "; ?> 
                        <?php echo "role=\"".$player->getRole()."\" "; ?>
                        <?php echo "name=\"".$player->getName()."\" "; ?>
                          <?php

                      if($roster->searchPlayer($player->getId())!=null){ ?> style="display:none;" in-roster="yes" <?php }
                          ?>
                          >
                          <td class="role-icon"><span <?php echo "class=\"".strtolower($player->getRole())."-but\" "; ?> ><?php echo strtoupper($player->getRole()); ?></td>
                          <td class="name-new-player"><?php echo $player->getName(); ?></td>
                          <td class="value-new-player"><?php echo $player->getValue(); ?></td>
                      </tr>  
                   <?php } ?>
                   </tbody>
                </table>
        
    
            </div>
        </div>

        
        
             
        
        
        <?php }else if(isset($_SESSION['username']) && $_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['ids'])){
            $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
            $user=$database->getUserByUsername($_SESSION['username']);

            $players=$database->dumpSingoliToList(null,null);
            $ids_string=$_POST['ids'];

            $database->createRoster($user,$players,$ids_string);
            $database->close();


        }
        
        ?>





    </div>


    


    <div id="footer"></div>

</div>

<script src="js/jquery-1.11.0.min.js"></script>
<script src="js/ion.rangeSlider.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js">


</script>

<script>
    var resetSlide;
    var list;

    $(document).ready(function(){

        $("#range_1").ionRangeSlider({
            min: 1,
            max: 50,
            from: 1,
            to: 50,
            type: 'double',
            step: 1,
            maxPostfix: "+",
            prettify: true,
            hasGrid: true,
            onChange: function(obj) {
                change(obj);
            }
        });

        var options = {
          valueNames: [ 'name-new-player', 'value-new-player' ],
          page: 1000
        };

        list = new List('players', options);



        $(function() { 

            function my_fun(){ 
               $("#range_1").ionRangeSlider("update", {
                    min: 1,                        // change min value
                    max: 50,                        // change max value
                    from: 1,                       // change default FROM setting
                    to: 50,                         // change default TO setting
                });
            }  
            resetSlide= my_fun;
            document.getElementById("search-element").value="";
            console.log(list.filter());
            console.log(list.search());
        }) 
        
    });
    
    var disable_but=function(bo){
        var mod_button=document.getElementById("mod-button");
        mod_button.disabled=bo;
    };
    
    var change = function(obj){
        var min=obj["fromNumber"];
        var max=obj["toNumber"];
        console.log(min);
        console.log(max);
        var players=document.getElementsByClassName("new-player");
        var dis="none";

        var element=document.getElementsByClassName('but-over');
        var role_select;
        for(i=0;i<element.length;++i){
            if(element[i].style.display=="none") {
                
            }else{
                role=element[i].parentNode.getElementsByClassName("but-role")[0].innerHTML;
            }
            
        }


        for (var i = 0; i < players.length; ++i) {
            var player = players[i];  
            var value=player.getAttribute("data-value");
            if(value<min || value>max){
                player.style.display="none";

            }else{
                if(role.toLowerCase()==player.getAttribute("role").toLowerCase()){
                    player.style.display="inline";
                }else{
                    player.style.display="none";
                }

            }

            
        }




        
        
    };


    

    


    var hover=function(obj){
        var element=obj.getElementsByClassName('but-over');
        element[0].style.display="block";
    }

    var stophover=function(obj){
        var element=obj.getElementsByClassName('but-over');
        element[0].style.display="none";
    }

    var select_role=function(role,obj){

        var element=document.getElementsByClassName('but-over');
        for(i=0;i<element.length;i++){
            element[i].style.display="none";
            element[i].parentNode.setAttribute("onmouseout","stophover(this)");
        }

        element=obj.getElementsByClassName('but-over');
        obj.removeAttribute("onmouseout");
        element[0].style.display="block";
        

        var free_table=document.getElementById("free-table");
        var arr=free_table.rows;
        for (i = 0; i < arr.length; i++) {
            var element=arr[i];
            if(element.getAttribute("role").toLowerCase() != role.toLowerCase()){
                element.style.display="none";

            }else{
                if(element.getAttribute("in-roster")=="yes"){
                    element.style.display="none";
                }else{
                    element.style.display="inline";
                }
            }
        };

        
        resetSlide();

    }




    var ol=null,ne=null;
    var remove_roster=function(pass){
        document.getElementById("search-element").value="";
        list.filter();
        list.search();
        var obj=pass.currentTarget;
        var free_table=document.getElementById("free-table");
        var id=+obj.id+"_free";
        var table_element=document.getElementById(id);

        var element=document.getElementsByClassName('but-over');
        var role_select;
        for(i=0;i<element.length;++i){
            if(element[i].style.display=="none") {
                
            }else{
                role=element[i].parentNode.getElementsByClassName("but-role")[0].innerHTML;
            }
            
        }


        if(role.toLowerCase()==table_element.getAttribute("role").toLowerCase()){
            table_element.style.display="inline";
            table_element.setAttribute("in-roster","none");
        }else{
            table_element.setAttribute("in-roster","none");
        }



        
        var index=obj.rowIndex;
        
        obj.parentNode.removeChild(obj);
        cost_change(obj.getAttribute("data-value"),false);
        
    };
    
    var add_roster=function(pass){
        var obj=pass.currentTarget;
        var table=document.getElementById(obj.getAttribute("role")+"_free");
        var value=obj.getAttribute("data-value");
        console.log("balance "+balance());
        console.log("value "+parseInt(value));
        if((balance()-parseInt(value))>=0){
            var max=parseInt(table.getAttribute("max"));
            var lenght_table=table.rows.length;
            console.log(table);
            console.log(max);
            console.log(lenght_table);
            if(lenght_table<max){
                var row=table.insertRow();
                row.className="old-player";
                row.id=obj.getAttribute("id_player");
                row.setAttribute("data-value",obj.getAttribute("data-value"));
                row.setAttribute("name",obj.getAttribute("name"));
                //row.setAttribute("onclick","javascript:alert(1);");
                var rolecell=row.insertCell();
                rolecell.innerHTML="<span class=\""+obj.getAttribute("role")+"-but\">"+obj.getAttribute("role")+"</span>";
                rolecell.className="role-icon";

                var namecell=row.insertCell();
                namecell.innerHTML=obj.getAttribute("name");
                var valuecell=row.insertCell();
                valuecell.innerHTML=obj.getAttribute("data-value");
                obj.style.display="none";
                obj.setAttribute("in-roster","yes");
                cost_change(value,true);
            };
        };

    };
    
    var getValues=function(){
        var jsonObj = [];
        var jsonString;
        var table = document.getElementById("P_free");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id");
            jsonObj.push(item);
        };
        
        var table = document.getElementById("D_free");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id");
            jsonObj.push(item);
        };
        
        var table = document.getElementById("C_free");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id");
            jsonObj.push(item);
        };
        
        var table = document.getElementById("A_free");
        for (var r = 0, n = table.rows.length; r < n; r++) {
            var item = table.rows[r].getAttribute("id");
            jsonObj.push(item);
        };
        
        jsonString = JSON.stringify(jsonObj);
       
       var url = 'createroster.php';
       var text='<form action="' + url + '" method="post">';
       
       for(var i=0, n=jsonObj.length;i<n;i++){
           text=text+'<input type="hidden" name="ids[]" value="'+jsonObj[i]+'" />';
       }
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
    
    var cost_change=function(cost,add){
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
    };
    
    
    $("body").on('click','.old-player',remove_roster);
        
      
    $("body").on('click','.new-player',add_roster);

</script>
</body>