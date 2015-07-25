<?php

/**
 * Ottiene una tabella con la classifica della competizione
 * @param  int 
 * @return string
 */
function getStandings($id_competition){

	$api=new ApiAccess(API_PATH);

    $standings=$api->accessApi("/competitions/".$id_competition."/standings" , "GET");

    $results = null;

    if($standings["error"]==false){
        $results = $standings["data"]["standings"];
    }
    
    
    $pos=1;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

    
        foreach($results as $team){

            $ret.="<div class=\"old-player\" id=\"".$team["team_info"]["id"]."\" >";
            $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
            $ret.="<div class=\"name-player-item\">".$team["team_info"]["name_team"]."</div>";
            $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team["points"]."</div>";
            $ret.="<div class=\"finalvote vote value-player-item\">".$team["gol"]."</div></div>";
            $ret.="</div>";

            $pos++; 
        }

    $ret.="</div>";

    return $ret;


}

/**
 * [getStandingsByIdUser description]
 * @param  [type]
 * @param  [type]
 * @return [type]
 */
function getStandingsByIdUser($id_competition,$id_user){

	$api=new ApiAccess(API_PATH);

    $standings=$api->accessApi("/competitions/".$id_competition."/standings" , "GET");

    $results = null;

    if($standings["error"]==false){
        $results = $standings["data"]["standings"];
    }
        
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

    
        foreach($results as $team){

            if($pos>3 && $team['id_user']!=$id_user){
                if(!$dots){
                    $ret.="<div class=\"old-player dots\">...</div>";
                    $dots=true;
                }
            }else{
                if($team['id_user']==$id_user) $ret.="<div class=\"old-player user_position\" id=\"".$team["team_info"]['id']."\" >";
                else $ret.="<div class=\"old-player\" id=\"".$team["team_info"]["id"]."\" >";
                $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
                $ret.="<div class=\"name-player-item\">".$team["team_info"]["name_team"]."</div>";
                $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team['points']."</div>";
	            $ret.="<div class=\"finalvote vote value-player-item\">".$team['gol']."</div></div>";
	            $ret.="</div>";
            }

            $pos++; 
        }

    $ret.="<div class=\"old-player finalvote complete-standing\" onclick=\"javascript:location.href='standings.php?competition=".$id_competition."'\">Classifica Completa &#8594 </div></div>";

    return $ret;


}


function getStandingsRound($id_competition,$id_round){
    

	$api=new ApiAccess(API_PATH);

    $config = $api->accessApi("/config" , "GET");


    if($id_round==-1) {
        $id_round = $config['last-round'];
    } 

    $competitions = $api->accessApi("/competitions" , "GET");
    if($competitions["error"] == false){
        if(isset($competitions["data"]["$id_competition"])){
            $compe=$competitions["data"]["$id_competition"];

            $id_round = $compe["competition_rounds"]["$id_round"];
        }
    }
    

    $standings=$api->accessApi("/competitions/".$id_competition."/standings/".$id_round , "GET");

    $results = null;

    if($standings["error"]==false){
        $results = $standings["data"]["standings"];
    }

    
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";


    foreach($results as $team){

        $ret.="<div class=\"old-player\" id=\"".$team["team_info"]["id"]."\" >";
        $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
        $ret.="<div class=\"name-player-item\">".$team["team_info"]["name_team"]."</div>";
        $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team["points"]."</div>";
        $ret.="<div class=\"finalvote vote value-player-item\">".$team["gol"]."</div></div>";
        $ret.="</div>";

        $pos++;
    }

    $ret.="</div>";

    return $ret;


}

function getStandingsRoundByIdUser($id_competition,$id_round,$id_user){ // ID_ROUND = COMPETITION ROUND
	
	$api=new ApiAccess(API_PATH);

    $config = $api->accessApi("/config" , "GET")["data"];


    if($id_round==-1) {
        $id_round = $config['last-round'];
    }

    $competitions = $api->accessApi("/competitions" , "GET");
    if($competitions["error"] == false){
        if(isset($competitions["data"]["$id_competition"])){
            $compe=$competitions["data"]["$id_competition"];

            $id_round = $compe["competition_rounds"]["$id_round"];
        }
    }
    
	$text = "/competitions/".$id_competition."/standings/".$id_round;

    $standings=$api->accessApi($text , "GET");

    $results = null;

    if($standings["error"]==false){
        $results = $standings["data"]["standings"];
    }
            
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";


    foreach($results as $team){

        if($pos>3 && $team['id_user']!=$id_user){
            if(!$dots){
                $ret.="<div class=\"old-player dots\">...</div>";
                $dots=true;
            }
        }else{
            if($team['id_user']==$id_user) $ret.="<div class=\"old-player user_position\" id=\"".$team["team_info"]['id']."\" >";
            else $ret.="<div class=\"old-player\" id=\"".$team["team_info"]["id"]."\" >";
            $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
            $ret.="<div class=\"name-player-item\">".$team["team_info"]["name_team"]."</div>";
            $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team['points']."</div>";
            $ret.="<div class=\"finalvote vote value-player-item\">".$team['gol']."</div></div>";
            $ret.="</div>";
        }

        $pos++;
    }

    $ret.="<div class=\"old-player finalvote complete-standing\" onclick=\"javascript:location.href='standings.php?competition=$id_competition&round=$id_round'\">Classifica Completa &#8594 </div></div>";

    return $ret;


}


function calc($stat,$role){
    $vote=$stat['vote']["value"];
    $scored=3*$stat['scored']["value"];
    $taken=1*$stat['taken']["value"];
    $free_keep=3*$stat['free_kick_keeped']["value"];
    $free_miss=1*$stat['free_kick_missed']["value"];
    $free_score=3*$stat['free_kick_scored']["value"];
    $auto=2*$stat['autogol']["value"];
    $yellow=0.5*$stat['yellow_card']["value"];
    $red=1*$stat['red_card']["value"];
    $assist=1*$stat['assist']["value"];
    $stop_assist=1*$stat['stop_assist']["value"];
    $gdp=0*$stat['gdp']["value"];
    $gdv=0*$stat['gdv']["value"];
    if($vote!=-1){
        $vote=$vote+$scored-$taken+$free_keep-$free_miss+$free_score-$auto-$yellow-$red+$assist+$stop_assist+$gdp+$gdv;
    }else if($vote==-1 && strtolower($role)=="p"){
        if($stat['red_card']["value"]==1){
            $vote=4;
        } // DA CONTROLLARE IL MINUTAGGIO
        //$vote=$vote+$scored-$taken+$free_keep-$free_miss+$free_score-$auto-$yellow-$red+$assist+$stop_assist+$gdp+$gdv;
    }else if($vote==-1 && strtolower($role)!="p"){
        if($stat['red_card']["value"]==1){
            $vote=4;
        }else if($stat['scored']["value"]>0 || $stat['free_kick_keeped']["value"]>0 || $stat['free_kick_scored']["value"]>0 || $stat['assist']["value"]>0 || $stat['stop_assist']["value"]>0){
            $vote=6;
            $vote=$vote+$scored+$free_keep+$free_score+$assist+$stop_assist;
        }else if($stat['free_kick_missed']["value"]>0 || $stat['autogol']["value"]>0){
            $vote=6;
            $vote=$vote-$free_miss-$auto;
        }else{
            $vote=-1;
        }
    }
    return $vote;
}



function role($string){
    if($string=='P') return 'Portiere';
    if($string=='D') return 'Difensore';
    if($string=='C') return 'Centrocampista';
    if($string=='A') return 'Attaccante';
}

function media($statistics){
    $vote=0;
    $number=0;
    foreach($statistics as $stat){
        if(isset($stat['final'])){
            if($stat['final']["value"]!=-1){
                $vote=$vote+$stat['final']["value"];
                $number++;
            }
        }
    }

    if($vote!=0) return ($vote/$number);
    return "N.D.";
}

function presenze($statistics){
    $number=0;
    foreach($statistics as $stat){
        if(isset($stat['final']) && $stat['final']["value"]!=-1){
            $number++;
        }
    }

    return $number;
}







?>