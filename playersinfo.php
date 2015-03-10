<?php
include('header.php');

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
			if($stat['final']->getValue()!=-1){
				$vote=$vote+$stat['final']->getValue();
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
		if(isset($stat['final']) && $stat['final']->getValue()!=-1){
			$number++;
		}
	}

	return $number;
}

?>
<script>
	var val=new Array();
	var lab=['Mon', 'Tue', 'Wed'];

	var vot=new Array();
	var fin=new Array();
	var days=new Array();

	var data;
	var datavote;




</script>
<?php


    $database=new ConnectDatabase("localhost","root","aicon07","fantacalcio",3306);
    $config=$database->dumpConfig();

    if(isset($_GET['id'])){
        $id_player=$_GET['id'];
    }

    if($player=$database->dumpPlayerById($id_player)){
	    $values=$database->getValuesOfPlayer($id_player);
	    

		$votes=array();
		$rounds_arr=array();
		$item=false;
		$it=1;

		$last_round=$database->getLastStatRound();

		foreach($player->getStat() as $stat){
			if(isset($stat['final'])){
				$vote=$stat['final']->getValue();
				$basic=$stat['vote']->getValue();
				$r=$stat['round']->getValue();

				if($it==$r){
					if($vote==-1) $vote=0;
					$votes[]=array('final'=>$vote,'round'=>$r,'vote'=>$basic);
					$item=true;
					$it++;
				}else{
					$colmare=$r-$it;
					for($i=0;$i<$colmare;$i++){
						$votes[]=array('final'=>0,'round'=>$it,'vote'=>0);
						$it++;
					}
					$it++;
					if($vote==-1) $vote=0;
					$votes[]=array('final'=>$vote,'round'=>$r,'vote'=>$basic);
					$item=true;
				}
			}
		}


		if($it<$last_round){
			for($i=$it;$i<$last_round;$i++){
				$votes[]=array('final'=>0,'round'=>$i+1,'vote'=>0);
			}
		}

		if(!$item) $votes[]=array('final'=>0,'round'=>0,'vote'=>0);


         ?>

         <script type="text/javascript">

		    var arr= <?php echo json_encode($values); ?>;
			var dates=new Array();



		    for(var i=0;i<arr.length;i++){
		        val.push(arr[i]['value']);
		        dates.push(arr[i]['date'])

		    }

		    lab=dates;

		    arr= <?php echo json_encode($votes); ?>;



		    for(var i=0;i<arr.length;i++){
		        vot.push(arr[i]['vote']);
		        fin.push(arr[i]['final']);
		        days.push(arr[i]['round']);

		    }



		 </script>


        <div class="box value_box">
	        <div class="name_box">
				<span class="name_item"><?php echo $player->getName(); ?></span>
		        <span class="role_item"><?php echo role($player->getRole()); ?></span>
	        </div>

	        <div class="team_box">
				<img <?php echo "src=\"teamlogo/".$player->getTeam().".png\""; ?> />
		        <span class="team_item"><?php echo $player->getTeam(); ?></span>
	        </div>

	        <div class="media_box"><span class="descript_item media_item">Media:</span><span class="value_item media_value_item"><?php echo round(media($player->getStat()),2); ?></span></div>
	    	<div class="current_value_box"><span class="descript_item">Costo Attuale:</span><span class="value_item"><?php echo $player->getValue(); ?></span></div>
	        <div class="media_box presenze_box"><span class="descript_item media_item">Presenze:</span><span class="value_item media_value_item"><?php echo presenze($player->getStat()); ?></span></div>
	        <div class="current_value_box first_value_box"><span class="descript_item">Costo Iniziale:</span><span class="value_item"><?php echo $player->getFirstValue(); ?></span></div>
        </div>



         <div class="value_info_box box ">
		 <canvas id="val_chart" class="value_chart" width="1080" height="300"></canvas>

        </div>

		<?php if(count($votes)!=0){ ?>

         <div class="value_stat_box box ">
			<table>
				<tr class="value_tr"><th>Giornata</th><th>Voto</th><th>Segnati</th><th>Subiti</th><th>Rigori Segnati</th><th>Rigori Parati</th><th>Rigori Sbagliati</th><th>Autogol</th><th>Giallo</th><th>Rosso</th><th>Assist</th><th>Fanta Voto</th></tr>
				<?php foreach($player->getStat() as $stat) { ?>
					<tr class="value_tr">
						<td><?php echo $stat['round']->getValue(); ?></td>
						<td><?php echo $stat['vote']->getValue(); ?></td>
						<td><?php echo $stat['scored']->getValue(); ?></td>
						<td><?php echo $stat['taken']->getValue(); ?></td>
						<td><?php echo $stat['free_kick_scored']->getValue(); ?></td>
						<td><?php echo $stat['free_kick_keeped']->getValue(); ?></td>
						<td><?php echo $stat['free_kick_missed']->getValue(); ?></td>
						<td><?php echo $stat['autogol']->getValue(); ?></td>
						<td><?php echo $stat['yellow_card']->getValue(); ?></td>
						<td><?php echo $stat['red_card']->getValue(); ?></td>
						<td><?php echo $stat['assist']->getValue(); ?></td>
						<td><?php echo $stat['final']->getValue(); ?></td>
					</tr>
				<?php } ?>
			</table>
        </div>

        <div class="value_info_box box ">
		 <canvas id="vote_chart" class="vote_chart value_chart" width="1080" height="200"></canvas>
        </div>
        <div class="box tables_box">
	        <div class="value_info_table_box">
				<span class="name_item">Quotazioni</span>
				<table>
					<tr class="value_tr"><th>Giornata</th><th>Valore</th></tr>
					<?php foreach($values as $val) { ?>
						<tr class="value_tr"><td><?php echo $val['date']; ?></td><td><?php echo $val['value']; ?></td></tr>
					<?php } ?>
				</table>
	        </div>
	        <div class="vote_info_table_box value_info_table_box">
				<span class="name_item">Voti</span>
				<table>
					<tr class="value_tr"><th>Giornata</th><th>Voto</th></tr>
					<?php $values=array_reverse($votes); foreach($votes as $val) { ?>
						<tr class="value_tr"><td><?php echo $val['round']; ?></td><td><?php echo $val['vote']; ?></td></tr>
					<?php } ?>
				</table>
	        </div>
        </div>

        <?php }else{ ?>
        	<div class="error_display">Questo giocatore non ha mai ricevuto un voto</div>
        <?php } ?>


<?php } else { //FINE SE USER NON ESISTE ?>
<div class="error_display">Errore , non esiste questo giocatore </div>
<?php } ?>
<script>
    $(document).ready(function(){

        var options={

	    ///Boolean - Whether grid lines are shown across the chart
	    scaleShowGridLines : true,

	    //String - Colour of the grid lines
	    scaleGridLineColor : "rgba(0,0,0,.05)",

	    //Number - Width of the grid lines
	    scaleGridLineWidth : 1,

	    //Boolean - Whether the line is curved between points
	    bezierCurve : false,

	    //Number - Tension of the bezier curve between points
	    //bezierCurveTension : 0.4,

	    //Boolean - Whether to show a dot for each point
	    pointDot : true,

	    //Number - Radius of each point dot in pixels
	    pointDotRadius : 4,

	    //Number - Pixel width of point dot stroke
	    pointDotStrokeWidth : 1,

	    //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
	    pointHitDetectionRadius : 20,

	    //Boolean - Whether to show a stroke for datasets
	    datasetStroke : true,

	    //Number - Pixel width of dataset stroke
	    datasetStrokeWidth : 2,

	    //Boolean - Whether to fill the dataset with a colour
	    datasetFill : true,

	    //String - A legend template
	    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"

	};

	var data = {
	  // A labels array that can contain any sort of values
	  labels: lab,
	  // Our series array that contains series objects or in this case series data arrays
	  datasets: [{
		label: "Quotazioni",
        fillColor: "rgba(220,220,220,0.2)",
        strokeColor: "#933131",
        pointColor: "#933131",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: val
        }]
	};

	console.log(val);
	console.log(lab);

	var options=null;

	// As options we currently only set a static size of 300x200 px. We can also omit this and use aspect ratio containers
	// as you saw in the previous example


	// Create a new line chart object where as first parameter we pass in a selector
	// that is resolving to our chart container element. The Second parameter
	// is the actual data object. As a third parameter we pass in our custom options.
	//new Chartist.Line('.value_chart', data, options);

	var data2 = {
	  // A labels array that can contain any sort of values
	  labels: days,
	  // Our series array that contains series objects or in this case series data arrays
	  datasets: [{
		label: "Fanta Voti",
        fillColor: "rgba(220,220,220,0.2)",
        strokeColor: "#0065fb",
        pointColor: "#0065fb",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: fin
        },
        {
		label: "Voti",
        fillColor: "rgba(220,220,220,0.2)",
        strokeColor: "#000",
        pointColor: "#000",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(220,220,220,1)",
        data: vot
        }]
	};


	//new Chartist.Line('.vote_chart',datavote,options);

	var ctx = document.getElementById("val_chart").getContext("2d");
	var myNewChart = new Chart(ctx).Line(data,options);

	var ctx2 = document.getElementById("vote_chart").getContext("2d");
	var myNewChart = new Chart(ctx2).Line(data2,options);
});

</script>
<?php include('footer.php'); ?>