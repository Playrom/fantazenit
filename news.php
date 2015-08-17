<?php
$title = "News";
include('header.php');

if(isset($_GET['id'])){
    $id=$_GET['id']; 
    $json=$apiAccess->accessApi("/news/".$id,"GET");
    $news=null;


    if($json["error"] == false){
        $news=$json["data"];
        $title = $news["title"];
        echo "<script>document.title=\"Fanta Zenit - $title\";</script>";
    }else{
        $error_messages[] = "Nessuna Notizia con questo ID";
    }
    
    
	include('error-box.php');
	
	if($news!=null){ ?>
		<div class="container-fluid">    
		    <div class="row presentation motivi">
			    <div class="col-md-12">
				    <h1><?php echo $news["title"]; ?></h1>
				    <?php echo $news["html"]; ?>
			    </div>
		    </div>
		</div>

	<?php
	}
}else{ // SE DEVO LEGGERLE TUTTE
	$json=$apiAccess->accessApi("/news","GET");
    $news=null;


    if($json["error"] == false){
        $news=$json["data"];
    }else{
        $error_messages[] = "Nessuna Notizia Pubblicata";
    }
    
    if($news!=null && count($news)>0){ ?>
	    <div class="container-fluid">
		    <div class="news">
	    
		    <?php
			foreach($news as $item){ ?>
			    <div class="row presentation motivi" style="max-height: 500px; overflow: hidden;">
					<div class="excerpt-mask"></div>
				    <div class="col-md-12">
					    <a href="news.php?id=<?php echo $item["id"]; ?>" ><h1><?php echo $item["title"]; ?></h1></a>
					    <?php echo $item["html"]; ?>
				    </div>
			    </div>
		    <?php
			}
			?>
			
		    </div>
	    </div>
	    
	<?php
	    
    }

}

?>

<?php include('footer.php'); ?>