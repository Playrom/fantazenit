<?php

/**
 * Ottiene una tabella con la classifica della competizione
 * @param  int 
 * @return string
 */
function getStandings($id_competition){
    $database = new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $database_users = new ConnectDatabaseUsers($database->mysqli);
    $database_competitions = new ConnectDatabaseCompetitions($database->mysqli);

    $results=$database_competitions->getStandings($id_competition);
    
    
    $pos=1;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

    
        foreach($results as $team){
            $teamData=$database_users->getUserById($team['id_user']);

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
    $database = new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $database_users = new ConnectDatabaseUsers($database->mysqli);
    $database_competitions = new ConnectDatabaseCompetitions($database->mysqli);

    $results=$database_competitions->getStandings($id_competition);
        
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

    
        foreach($results as $team){
            $teamData=$database_users->getUserById($team['id_user']);
            
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
    $database = new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $database_users = new ConnectDatabaseUsers($database->mysqli);
    $database_competitions = new ConnectDatabaseCompetitions($database->mysqli);
    $database_rounds = new ConnectDatabaseRounds($database->mysqli);
    
    $config=$database->dumpConfig();
    
    if($id_round==-1) $id_round=$config['last-round'];
    else $id_round=$database_rounds->getRealRoundByRoundCompetition($id_round,$id_competition);
    $results=$database_rounds->getRoundStandings($id_competition,$id_round);
    
    
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

        foreach($results as $team){
            $teamData=$database_users->getUserById($team['id_user']);

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
    $database = new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $database_users = new ConnectDatabaseUsers($database->mysqli);
    $database_competitions = new ConnectDatabaseCompetitions($database->mysqli);
    $database_rounds = new ConnectDatabaseRounds($database->mysqli);

    $config=$database->dumpConfig();

    $base_round=$id_round;
    if($id_round==-1) { 
        $id_round=$config['last-round'];
        $base_round=$database_rounds->getRoundCompetitionByRealRound($id_round,$id_competition);
    }else{
        $id_round=$database_rounds->getRealRoundByRoundCompetition($id_round,$id_competition);
    }
    $results=$database_rounds->getRoundStandings($id_competition,$id_round);
    
    
    $pos=1;
    $dots=false;

    $ret="<div class=\"roster-item\"><div class=\"old-player info_player\"><div class=\"role-icon\">*</div><div class=\"name-player-item\">Nome Squadra</div>";
    $ret.="<div class=\"info-player-item\"><div class=\"vote value-player-item\">Punti</div><div class=\"finalvote vote value-player-item\">Gol</div></div></div>";

    
        foreach($results as $team){
            $teamData=$database_users->getUserById($team['id_user']);
            
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


function calc($stat,$role){
    $vote=$stat['vote']->getValue();
    $scored=3*$stat['scored']->getValue();
    $taken=1*$stat['taken']->getValue();
    $free_keep=3*$stat['free_kick_keeped']->getValue();
    $free_miss=1*$stat['free_kick_missed']->getValue();
    $free_score=3*$stat['free_kick_scored']->getValue();
    $auto=2*$stat['autogol']->getValue();
    $yellow=0.5*$stat['yellow_card']->getValue();
    $red=1*$stat['red_card']->getValue();
    $assist=1*$stat['assist']->getValue();
    $stop_assist=1*$stat['stop_assist']->getValue();
    $gdp=0*$stat['gdp']->getValue();
    $gdv=0*$stat['gdv']->getValue();
    if($vote!=-1){
        $vote=$vote+$scored-$taken+$free_keep-$free_miss+$free_score-$auto-$yellow-$red+$assist+$stop_assist+$gdp+$gdv;
    }else if($vote==-1 && strtolower($role)=="p"){
        if($stat['red_card']->getValue()==1){
            $vote=4;
        } // DA CONTROLLARE IL MINUTAGGIO
        //$vote=$vote+$scored-$taken+$free_keep-$free_miss+$free_score-$auto-$yellow-$red+$assist+$stop_assist+$gdp+$gdv;
    }else if($vote==-1 && strtolower($role)!="p"){
        if($stat['red_card']->getValue()==1){
            $vote=4;
        }else if($stat['scored']->getValue()>0 || $stat['free_kick_keeped']->getValue()>0 || $stat['free_kick_scored']->getValue()>0 || $stat['assist']->getValue()>0 || $stat['stop_assist']->getValue()>0){
            $vote=6;
            $vote=$vote+$scored+$free_keep+$free_score+$assist+$stop_assist;
        }else if($stat['free_kick_missed']->getValue()>0 || $stat['autogol']->getValue()>0){
            $vote=6;
            $vote=$vote-$free_miss-$autogol;
        }else{
            $vote=-1;
        }
    }
    return $vote;
}









?>