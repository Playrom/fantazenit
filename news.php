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
}

?>

<?php include('footer.php'); ?>