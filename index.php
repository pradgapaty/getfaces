 <?php

require_once("web/main.php"); 
require_once("/var/www/html/ParsePhoto/Helpers/LoaderHelper.php");

$parseThispersonClassInst = new ParseThisperson();
$getPhotoClassInst = new GetPhoto();
?>

<!DOCTYPE html>
<head>
	<meta charset="utf-8">
    <meta name="description" CONTENT="Free people photos with faces. You can use filter by gender and age. You can get any photo for self.">
    <meta name="robots" content="all">
<style>
 	div.section_header {
 		padding: 3px 6px 3px 6px;
 		background-color: #8E9CB2;
 		color: #FFFFFF;
 		font-weight: bold;
 		font-size: 112%;
 		text-align: center;
 	}
 	.deleteDiv {
	    background: white;
	    color: red;
	    font-family: 'Helvetica', 'Arial', sans-serif;
	    font-size: 2em;
	    font-weight: bold;
	    text-align: center;
	    width: 40px;
	    height: 40px;
	    border-radius: 5px;
 	}
 	pre, code{
 		white-space:pre-line;
 	}
 	.contain {
		position: relative;
		background: black;
	}
	.contain .photoSave {
		position: absolute;
		top: 26px;
		right: 438px;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
		background-color: #555;
		color: white;
		font-size: 16px;
		padding: 12px 24px;
		border: none;
		cursor: pointer;
		border-radius: 5px;
		font-weight: bold;
	}
	.contain .photoLike {
		position: absolute;
		top: 76px;
		right: 514px;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
		background-color: #0BDA51;
		color: white;
		font-size: 16px;
		padding: 12px 24px;
		border: none;
		cursor: pointer;
		border-radius: 5px;
		font-weight: bold;
	}
	.contain .photoDislike {
		position: absolute;
		top: 126px;
		right: 487px;
		transform: translate(-50%, -50%);
		-ms-transform: translate(-50%, -50%);
		background-color: #D3212D;
		color: white;
		font-size: 16px;
		padding: 12px 24px;
		border: none;
		cursor: pointer;
		border-radius: 5px;
		font-weight: bold;
	}

	.contain .photoButton:hover {
		background-color: black;
	}
</style>
<script type="text/javascript">


function like(photoId, iterator) {
	var ipAddress = "<?php echo($_SERVER["REMOTE_ADDR"]); ?>";

	$.ajax({
	    type: 'POST',
	    data: {
	    	"action" : "like",
	    	"ip" : ipAddress,
	    	"photoId" : photoId},
		url: 'Helpers/LikeHelper.php',
	    dataType: 'json',
        cache: false,

 		success: function(response, status, xhr) {
 			if (response) {
 				window.alert(response);
 			} else {
	 			var locatorLike = 'likePhoto_' + iterator;
	 			var locatorDislike = 'dislikePhoto_' + iterator;
	 			document.getElementById(locatorLike).style.display = 'none';
	 			document.getElementById(locatorDislike).style.display = 'none';
 			}
        },
  		error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                window.alert("Cannot like photo! Something wrong . . .");
        }
  	});
}

function dislike(photoId, iterator) {
	var ipAddress = "<?php echo($_SERVER["REMOTE_ADDR"]); ?>";

	$.ajax({
	    type: 'POST',
	    data: {
	    	"action" : "dislike",
	    	"ip" : ipAddress,
	    	"photoId" : photoId},
	    url: 'Helpers/LikeHelper.php',
	    dataType: 'json',
        cache: false,

 		success: function(response, status, xhr) {
 			 if (response) {
 				window.alert(response);
 			} else {
	 			var locatorLike = 'likePhoto_' + iterator;
	 			var locatorDislike = 'dislikePhoto_' + iterator;
	 			document.getElementById(locatorLike).style.display = 'none';
	 			document.getElementById(locatorDislike).style.display = 'none';
 			}
        },
  		error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
            window.alert("Cannot dislike photo! Something wrong . . .");
        }
  	});
}

</script>
</head>
<body>
	<div id="fh5co-wrapper">
		<div id="fh5co-page">
			<div id="fh5co-header">
				<div class="top">
					<div class="container">
					</div>
				</div>
				<!-- end:top -->
				<header id="fh5co-header-section">
					<div class="container">
						<div class="nav-header">
							<a href="#" class="js-fh5co-nav-toggle fh5co-nav-toggle"><i></i></a>
							<h1 id="fh5co-logo"><a href="/">getfaces.ml</a></h1>
							<!-- START #fh5co-menu-wrap -->
							<nav id="fh5co-menu-wrap" role="navigation">
								<?php $page = 'home'; include("web/menu.php");
								?>
							</nav>
						</div>
					</div>
				</header>
			</div>
			<div class="fh5co-hero">
				<div class="fh5co-overlay"></div>
				<div class="fh5co-cover text-center" style="background-image: url(/web/img/light-blue-background-abstract-design-260nw-121557973.jpg);">
					<div class="desc animate-box">
						<h2>get faces</h2>
						<label>Total photo: <?php echo $parseThispersonClassInst->getPhotoCount(); ?></label>
					</div>
				</div>
			</div>
			<form class="well form-report"  action="" method=post style="text-align: center">
				<br>
				<label>Gender</label>
				<select name="gender" id="gender">
					<option value="male"<?php if($_POST['gender'] === 'male') echo "selected='selected'"; ?>>Male</option>
					<option value="female"<?php if($_POST['gender'] === 'female') echo "selected='selected'"; ?>>Female</option>
				</select>
				&emsp;
				<label>Age</label>
				<select name="age" id="age">
					<option value="1825"<?php if($_POST['age'] === '1825') echo "selected='selected'"; ?>>18 - 25</option>
					<option value="2635"<?php if($_POST['age'] === '2635') echo "selected='selected'"; ?>>26 - 35</option>
					<option value="3645"<?php if($_POST['age'] === '3645') echo "selected='selected'"; ?>>36 - 45</option>
					<option value="4660"<?php if($_POST['age'] === '4660') echo "selected='selected'"; ?>>46 - 60</option>
					<option value="6199"<?php if($_POST['age'] === '6199') echo "selected='selected'"; ?>>60+</option>
				</select>
				&emsp;
				<label>Limit</label>
				<select name="limit" id="limit">
					<option value="10"<?php if($_POST['limit'] === '10') echo "selected='selected'"; ?>>10</option>
					<option value="20"<?php if($_POST['limit'] === '20') echo "selected='selected'"; ?>>20</option>
					<option value="50"<?php if($_POST['limit'] === '50') echo "selected='selected'"; ?>>50</option>
					<option value="100"<?php if($_POST['limit'] === '100') echo "selected='selected'"; ?>>100</option>
				</select>
				&emsp;
				<label>Random</label>
				<input type="checkbox" id="random" name="random" <?php if($_POST['random']) echo "checked='checked'"; ?>>
				<br>
				<br>
				<br>
				<input id="getProfiles" type="submit" value="Get info" name="submit" class="button btn">
        		<!-- <input type="button" id="rehashButton" onclick="SubmitFormData();" value="Rehash Photo" name="rehash" class="button btn"> -->
        		<div id="status"></div>
			</div>
		</form>  


		<div class="row row-bottom-padded-lg">
			<div class="col-md-12 animate-box">
				<div class="section_header">
					<?php
					if (!empty($_POST['submit'])) {
						$ageFrom = substr($_POST['age'], 0, 2);
						$ageTo = substr($_POST['age'], 2, 2);

						$filter = [
							'gender' => $_POST['gender'],
							'fromAge' => $ageFrom,
							'toAge' => $ageTo,
							'limit' => $_POST['limit'],
							'random' => $_POST['random'],
						];

						$photoData = $parseThispersonClassInst->getPhotoData($filter);
						echo '<div id="changes">' . $_POST['gender'] . ' with age range [' . $ageFrom . ' - ' . $ageTo .']. Count :: ' . count($photoData) . '</div></div>';
						echo '<div class="demo" style="text-align:center">';
						echo '<ul id="lightSlider">';

						if (!empty($photoData)) {
							$i = 0;
							foreach ($photoData as $photoId) {
								$pathPrefix = $getPhotoClassInst->getPathPrefix($photoId);
								$imgUrl = "http://" . $_SERVER['HTTP_HOST']  . "/photoStore/" . $pathPrefix . "/" . $photoId . ".jpg";
								$imgThumbUrl = "http://" . $_SERVER['HTTP_HOST'] ."/thumbStore/" . $photoId . "_thumb.jpg";
								echo '<li data-thumb="' . $imgThumbUrl  . '">
								<div class="contain">
								<img src="' .  $imgUrl . '" style="width:640px" />
								<a class="photoSave" href="' .  $imgUrl . '" download> Save image</a>
								<a class="photoLike" id="likePhoto_' . $i . '" onclick="like(\'' . $photoId . '\',' . $i . ')">Like</a>
								<a class="photoDislike" id="dislikePhoto_' . $i . '" onclick="dislike(\'' . $photoId . '\',' . $i . ')"> Dislike</a>
								</div>
								</li>';
								$i++;
							}
						}
						echo '</ul>';
						echo '</div>';
					}
					?>
					<script type="text/javascript">
					  $(document).ready(function() {
						    $('#lightSlider').lightSlider({
						    gallery: true,
						    item: 1,
						    loop:true,
						    slideMargin: 0,
						    thumbItem: 10
					});
			        $('html, body').animate({
			        	scrollTop: $("#lightSlider").offset().top}, 2000);
			    	});
					</script>
				</div>
			</div>
			<? include('web/footer.php'); ?>
		</div>
		</div>
</body>
</html>
