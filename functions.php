<?php

/**
 * Ottiene una tabella con la classifica della competizione
 * @param  int 
 * @return string
 */
function getStandings($id_competition){
    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $results=$database->getStandings($id_competition);
    
    
    $pos=1;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

    
        foreach($results as $team){
            $teamData=$database->getUserById($team['id_user']);

            $ret.="<div class=\"old-player\" id=\"".$teamData->getId()."\" >";
            $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
            $ret.="<div class=\"name-player-item\">".$teamData->getNameTeam()."</div>";
            $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team['points']."</div>";
            $ret.="<div class=\"finalvote vote value-player-item\">".$team['gol']."</div></div>";
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
    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $results=$database->getStandings($id_competition);
        
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

    
        foreach($results as $team){
            $teamData=$database->getUserById($team['id_user']);
            
            if($pos>3 && $team['id_user']!=$id_user){
                if(!$dots){
                    $ret.="<div class=\"old-player dots\">...</div>";
                    $dots=true;
                }
            }else{
                if($team['id_user']==$id_user) $ret.="<div class=\"old-player user_position\" id=\"".$teamData->getId()."\" >";
                else $ret.="<div class=\"old-player\" id=\"".$teamData->getId()."\" >";
                $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
                $ret.="<div class=\"name-player-item\">".$teamData->getNameTeam()."</div>";
                $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team['points']."</div>";
	            $ret.="<div class=\"finalvote vote value-player-item\">".$team['gol']."</div></div>";
	            $ret.="</div>";
            }

            $pos++; 
        }

    $ret.="<div class=\"old-player finalvote complete-standing\" onclick=\"javascript:location.href='standings.php?competition=".$id_competition."'\">Classifica Completa -> </div></div>";

    return $ret;


}


function getStandingsRound($id_competition,$id_round){
    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $config=$database->dumpConfig();
    
    if($id_round==-1) $id_round=$config['last-round'];
    else $id_round=$database->getRealRoundByRoundCompetition($id_round,$id_competition);
    $results=$database->getRoundStandings($id_competition,$id_round);
    
    
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

        foreach($results as $team){
            $teamData=$database->getUserById($team['id_user']);

            $ret.="<div class=\"old-player\" id=\"".$teamData->getId()."\" >";
            $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
            $ret.="<div class=\"name-player-item\">".$teamData->getNameTeam()."</div>";
            $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team['points']."</div>";
            $ret.="<div class=\"finalvote vote value-player-item\">".$team['gol']."</div></div>";
            $ret.="</div>";

            $pos++; 
        }

    $ret.="</div>";

    return $ret;


}

function getStandingsRoundByIdUser($id_competition,$id_round,$id_user){
    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $config=$database->dumpConfig();

    $base_round=$id_round;
    if($id_round==-1) { 
        $id_round=$config['last-round'];
        $base_round=$database->getRoundCompetitionByRealRound($id_round,$id_competition);
    }else{
        $id_round=$database->getRealRoundByRoundCompetition($id_round,$id_competition);
    }
    $results=$database->getRoundStandings($id_competition,$id_round);
    
    
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

    
        foreach($results as $team){
            $teamData=$database->getUserById($team['id_user']);
            
            if($pos>3 && $team['id_user']!=$id_user){
                if(!$dots){
                    $ret.="<div class=\"old-player dots\">...</div>";
                    $dots=true;
                }
            }else{
                if($team['id_user']==$id_user) $ret.="<div class=\"old-player user_position\" id=\"".$teamData->getId()."\" >";
                else $ret.="<div class=\"old-player\" id=\"".$teamData->getId()."\" >";
                $ret.="<div class=\"role-icon\"><span class=\"p-but\" >".$pos."</span></div>";
                $ret.="<div class=\"name-player-item\">".$teamData->getNameTeam()."</div>";
                $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">".$team['points']."</div>";
	            $ret.="<div class=\"finalvote vote value-player-item\">".$team['gol']."</div></div>";
	            $ret.="</div>";
            }

            $pos++; 
        }

    $ret.="<div class=\"old-player finalvote complete-standing\" onclick=\"javascript:location.href='standings.php?competition=".$id_competition."&round=".$base_round."'\">Classifica Completa -> </div></div>";

    return $ret;


}








?>