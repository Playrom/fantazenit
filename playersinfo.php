<?php
include('header.php');

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


    if(isset($_GET['id'])){
        $id_player=$_GET['id'];
    }

    $json=$apiAccess->accessApi("/players/$id_player","GET");

    if($json["data"]!=null){
        $player = $json["data"];

	    $json=$apiAccess->accessApi("/players/$id_player/values","GET");

        $values = null;

        if($json["data"]!=null){
            $values = $json["data"];
        }
	    

		$votes=array();
		$rounds_arr=array();
		$item=false;
		$it=1;

		$last_round=$database_rounds->getLastStatRound();

		foreach($player["stat"] as $stat){
			if(isset($stat['final'])){
				$vote=$stat['final']["value"];
				$basic=$stat['vote']["value"];
				$r=$stat['round']["value"];

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
				<span class="name_item"><?php echo $player["name"]; ?></span>
		        <span class="role_item"><?php echo role($player["role"]); ?></span>
	        </div>

	        <div class="team_box">
				<img <?php echo "src=\"teamlogo/".$player["team"].".png\""; ?> />
		        <span class="team_item"><?php echo $player["team"]; ?></span>
	        </div>

	        <div class="media_box"><span class="descript_item media_item">Media:</span><span class="value_item media_value_item"><?php echo round(media($player["stat"]),2); ?></span></div>
	    	<div class="current_value_box"><span class="descript_item">Costo Attuale:</span><span class="value_item"><?php echo $player["value"]; ?></span></div>
	        <div class="media_box presenze_box"><span class="descript_item media_item">Presenze:</span><span class="value_item media_value_item"><?php echo presenze($player["stat"]); ?></span></div>
	        <div class="current_value_box first_value_box"><span class="descript_item">Costo Iniziale:</span><span class="value_item"><?php echo $player["first_value"]; ?></span></div>
        </div>



         <div class="value_info_box box ">
		 <canvas id="val_chart" class="value_chart" width="1080" height="300"></canvas>

        </div>

		<?php if(count($votes)!=0){ ?>

         <div class="value_stat_box box ">
			<table>
				<tr class="value_tr"><th>Giornata</th><th>Voto</th><th>Segnati</th><th>Subiti</th><th>Rigori Segnati</th><th>Rigori Parati</th><th>Rigori Sbagliati</th><th>Autogol</th><th>Giallo</th><th>Rosso</th><th>Assist</th><th>Fanta Voto</th></tr>
				<?php foreach($player["stat"] as $stat) { ?>
					<tr class="value_tr">
						<td><?php echo $stat['round']["value"]; ?></td>
						<td><?php echo $stat['vote']["value"]; ?></td>
						<td><?php echo $stat['scored']["value"]; ?></td>
						<td><?php echo $stat['taken']["value"]; ?></td>
						<td><?php echo $stat['free_kick_scored']["value"]; ?></td>
						<td><?php echo $stat['free_kick_keeped']["value"]; ?></td>
						<td><?php echo $stat['free_kick_missed']["value"]; ?></td>
						<td><?php echo $stat['autogol']["value"]; ?></td>
						<td><?php echo $stat['yellow_card']["value"]; ?></td>
						<td><?php echo $stat['red_card']["value"]; ?></td>
						<td><?php echo $stat['assist']["value"]; ?></td>
						<td><?php echo $stat['final']["value"]; ?></td>
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