<?php
include('headerlista.php');
?>

<?php


            $user=$database->getUserByUsername($username);

            $players=$database->dumpSingoliToList(null, null);

            $database->close();
    ?>
    <div id="team-info">

        <div class="name-team">La Listona del Fanta Zenit</div>

    </div>

        <div id="side-players" style="margin: 10px 10px 10px 10px;width: 1080px;">
        <div id="utility-row">
            <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)" onclick="select_role('P',this)"><div class="but-role p-but">P</div><div class="but-over p-but"></div></div>
            <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)" onclick="select_role('D',this)"><div class="but-role d-but">D</div><div class="but-over d-but"></div></div>
            <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)"  onclick="select_role('C',this)"><div class="but-role c-but">C</div><div class="but-over c-but"></div></div>
            <div class="but" onmouseover="hover(this)" onmouseout="stophover(this)"  onclick="select_role('A',this)"><div class="but-role a-but">A</div><div class="but-over a-but"></div></div>
            <div id="value-selector"><input type="text" id="range_1" /></div>

        </div>

        <div id="players">

            <div id="search-box"><input class="search" id="search-element" placeholder="Cerca">
                <button id="sort-name-button" class="sort" data-sort="name_new_player">Ordina per Nome</button>
                <button id="sort-value-button" class="sort" data-sort="value_new_player">Ordina per Costo</button>
            </div>

            <table id="free-table">
                <tbody class="list">
                  <?php  foreach($players as $player){   ?>
                  <tr class="new-player" <?php echo "id=\"".$player->getId()."_free\" "; ?> onclick="window.location ='playersinfo.php?id=<?php echo $player->getId(); ?>'"
                   <?php echo "id_player=\"".$player->getId()."\" "; ?>
                    class="free-player"
                    <?php echo "data-value=\"".$player->getValue()."\" "; ?>
                    <?php echo "role=\"".$player->getRole()."\" "; ?>
                    <?php echo "name=\"".$player->getName()."\" "; ?>

                      >
                    <td class="name_new_player"><span <?php echo "class=\"".strtolower($player->getRole())."-but icon\" "; ?> ><?php echo strtoupper($player->getRole()); ?></span><span class="name"><?php echo $player->getName(); ?></span></td>
                    <td class="value_new_player"><?php echo $player->getValue(); ?></td>
                    </tr>

               <?php } ?>
               </tbody>
            </table>


        </div>
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
                list.filter(function(item){
                    var min=obj["fromNumber"];
                    var max=obj["toNumber"];
                    if (item.values().value_new_player >= min && item.values().value_new_player <= max) {
                       return true;
                   } else {
                       return false;
                   }

                });
            }
        });

        var options = {
          valueNames: [ 'name_new_player', 'value_new_player' ],
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
                document.getElementById("search-element").value="";
                list.filter();
                list.search();
            }
            resetSlide= my_fun;

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
            console.log(i+element[i].style.display);
            if(element[i].style.display=="none") {
                role_select="none";
            }else{
                role_select=element[i].parentNode.getElementsByClassName("but-role")[0].innerHTML;
                console.log(role_select);
            }

        }



        for (var i = 0; i < players.length; ++i) {
            var player = players[i];
            var value=player.getAttribute("data-value");
            if(value<min || value>max){
                player.style.display="none";

            }else{

                if(role_select!="none"){
                    if(role_select.toLowerCase()==player.getAttribute("role").toLowerCase() && player.getAttribute("in-roster")!="yes"){
                        player.style.display="inline";
                    }else{
                        player.style.display="none";
                    }
                }else{
                    if(player.getAttribute("in-roster")!="yes"){
                        player.style.display="inline";
                    }else{
                        player.style.display="none";
                    }
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


</script>




<?php include('footer.php'); ?>