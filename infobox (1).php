<?php
	
$image = getImage(basename($_SERVER['PHP_SELF']));

$json = $apiAccess->accessApi("/competitions/".$config["default_competition"]."/standings/last","GET");

$points_lead = null;
$id_user = null;
$user_temp = null;
$id_round = null;
$position = null;

if($json["error"]==false){
	$id_round = $json["data"]["round"];
	
	$te = $json["data"]["standings"][0];
	
	$user_temp = $te["team_info"];

	$points_lead = $te["points"];
			
	$json = $apiAccess->accessApi("/competitions/".$config["default_competition"]."/standings","GET");
	if($json["error"]==false){
		$position = $json["data"]["standings_by_user"][$user_temp["id"]];
	}
}

$news_info = null;

$json = $apiAccess->accessApi("/news","GET");
if($json["error"]==false){
	$news_info = $json["data"];
}


if($user_temp!=null){
	
?>

	<div class="container-fluid" style="margin: 10px 0;">
		<div class="row">
			<div class="col-md-6">
				<!--<div class="image-box" style="background: url(<?php echo $image; ?>) no-repeat;">
				</div>-->
				<div class="image-box white-with-padding" style="height: 100%;">
						<div class="user-info-item name-team" style="text-align: center;">Le Ultime Notizie</div>
					<?php
					foreach($news_info as $item){ ?>
						<div class="user-info-item"><a href="/news.php?id=<?php echo $item["id"]; ?>"><?php echo $item["title"]; ?></a></div>
					<?php
					}
					?>
				</div>
				
			</div>
			
			<div class="col-md-6">
				<div class="image-box white-with-padding">
					
					<div class="user-menu">
						
						<div class="user-info-item name-team" style="text-align: center;">Fanta Manager of The Week <small>(<?php echo $id_round; ?>° Giornata)</small></div>
						        	
			        	<div class="avatar">
				        	<?php
					        	if($user_temp["url_avatar"]!=null){ ?>
						        	<img <?php echo "src=\"".$user_temp["url_avatar"]."\""; ?> />
					        	<?php 
						        }else{ 
						        ?>
						        	<img src="img/default_avatar.png" />
					        	<?php
						        }
					        	
					        ?>
			        	</div>
			        	
			        	<div class="user-info">
				        	<div class="user-info-item username"><a href="/teams.php?id=<?php echo $user_temp["id"]; ?>" ><?php echo $user_temp["name"]." ". $user_temp["surname"]; ?></a></div>
							<div class="user-info-item name-team"><?php echo $user_temp["name_team"]; ?></div>
							<div class="user-info-item name-team"><?php echo $points_lead; ?> Punti</div>
							<div class="user-info-item name-team"><?php echo $position; ?>° Posizione in Classifica</div>
			        	</div>
			        	
		            </div>
					
					
				</div>
			</div>
		</div>
			
	</div>

<?php

}
	
function getImage($url){
	
	switch($url){
		case "storia.php":
			return "img/storia.jpg";
		default:
			return null;
	}
	
}

?>