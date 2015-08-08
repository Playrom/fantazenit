<?php
	
$image = getImage(basename($_SERVER['PHP_SELF']));

if($image!=null){
	
?>

	<div class="container-fluid" style="margin-bottom: 10px;">
		<div class="col-md-6">
			<div class="image-box" >
				<img <?php echo "src=\"".$image."\""; ?> >
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="image-box white-with-padding">
				<h2>Lavori In Corso</h2>
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