<!DOCTYPE html>
<?php 
require_once "web/main.php"; 
require_once("/var/www/html/getfaces/Helpers/LoaderHelper.php");

$parseThispersonClassInst = new ParseThisperson();

?>
<head>
</head>
<body>
	<div id="fh5co-wrapper">
		<div id="fh5co-page">
			<div id="fh5co-header">
				<div class="top">
					<div class="container">
<!-- 						<span> <a href="https://instagram.com/pradgapaty/"><i class="icon-instagram2"></i>#pradgapaty</a></span>
						<span> <a href="#"><i>@</i> pradgapaty@gmail.com</a></span>
						<span> <a href="tel://+380938310229"><i class="icon-mobile3"></i>+380938310229</a></span> -->
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
								<?php $page = "about"; include("web/menu.php"); ?>
							</nav>
						</div>
					</div>
				</header>
			</div>
			<div class="fh5co-hero">
				<div class="fh5co-overlay"></div>
				<div class="fh5co-cover text-center" style="background-image: url(/web/img/light-blue-background-abstract-design-260nw-121557973.jpg);">
					<div class="desc animate-box">
						<br>
						<br>
						<h2>About project</h2>
					</div>
				</div>
			</div>
			<div id="fh5co-services-section" class="border-bottom">
				<div class="container">
					<div class="row">
						<div class="col-md-3 animate-box">
							<h3 class="heading-section">What is this?</h3>
							<p>We provide for you free service with unique photos of the peoples faces. How it's work?</p>
						</div>
						<div class="col-md-9 col-sm-12">
							<div class="row">
								<div class="col-md-4 col-sm-4">
									<div class="services animate-box">
										<span><i class="icon-browser"></i></span>
										<h3>Step 1</h3>
										<p>Service gets photos from free service <a href="https://thispersondoesnotexist.com" target="_blank">thispersondoesnotexist.com</a>. It's the website, created by Silicon Valley software engineer Phillip Wang. Each time you visit it, the site generates a unique face–a head-on photo of a human dreamed up by computer code. The underlying code that made this possible, titled StyleGAN, was written by Nvidia. It’s just a face that has never existed before that moment.</p>
									</div>
								</div>
								<div class="col-md-4 col-sm-4">
									<div class="services animate-box">
										<span><i class="icon-tools"></i></span>
										<h3>Step 2</h3>
										<p>We do verification photo for correctly, unique and get him md5 using technical means on server side and stored him on disk and database. We have several queues for getting, store, analyze and check photos. </p>
									</div>
								</div>
								<div class="col-md-4 col-sm-4">
									<div class="services animate-box">
										<span><i class="icon-search"></i></span>
										<h3>Step 3</h3>
										<p>After prepare photo, it is send to cloud service for face biometric detection, where do analyze for get age and gender params.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- fh5co-services-section -->
			<div id="fh5co-content-section">
				<div class="container">
					<div class="row">
						<div class="col-md-6 col-sm-6">
							<div class="fh5co-testimonial text-center animate-box">
								<h2>Unique face photos</h2>
								<?php 
									$randomPhoto = $parseThispersonClassInst->getRandomPhoto();
								 ?> 
								<figure>
									<?php
										echo '<img src=thumbStore/' . $randomPhoto[0] . '_thumb.jpg alt="example">';
									?>
								</figure>
								<blockquote>
									<p>With our service you can always get a unique photo within service, using gender and age filter.</p>
								</blockquote>
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="fh5co-about-us animate-box">
								<h2 class="text-center">About Us</h2>
								<p><a href="http://getfaces.ml" target="_blank">www.getfaces.ml</a> was created by Alexander Krukovskiy in 2020, i am from Ukraine. One day i readed news about site which creates a person’s face with the help of artificial intelligence and wanted create service what get internet users unique photos by gender and age params. All data on site is for free. If you want and can help me evolve service, i'll be happy and grateful get donate.</p>
							</div>
							<p><span> <a href="mailto: pradgapaty@gmail.com"><i>@</i> pradgapaty@gmail.com</a></span>
						</div>
					</div>
				</div>
			</div>
			<!-- fh5co-content-section -->
<!-- 			<div id="fh5co-blog-section">
				<div class="container">
					<div class="row">
						<div class="col-lg-3 col-sm-12 animate-box">
							<h3 class="heading-section">Last updates:</h3>
						</div>
						<div class="col-lg-9 col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-md-4">
									<div class="fh5co-blog animate-box" style="background-image: url(images/blog-1.jpg);">
										<a class="image-popup" href="#">
											<div class="prod-title">
												<span>13.01.2020</span>
												<h3>45 Minimal Worksspace Rooms for Web Savvys</h3>
												<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
											</div>
										</a> 
									</div>
								</div>
								<div class="col-lg-4 col-md-4">
									<div class="fh5co-blog animate-box" style="background-image: url(images/blog-2.jpg);">
										<a class="image-popup" href="#">
											<div class="prod-title">
												<span>Illustration</span>
												<h3>Don’t Just Stand There</h3>
												<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
											</div>
										</a> 
									</div>
								</div>
								<div class="col-lg-4 col-md-4">
									<div class="fh5co-blog animate-box" style="background-image: url(images/blog-3.jpg);">
										<a class="image-popup" href="#">
											<div class="prod-title">
												<span>Illustration</span>
												<h3>Don’t Just Stand There</h3>
												<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
											</div>
										</a>
									</div>
								</div>
								<div class="col-lg-4 col-md-4">
									<div class="fh5co-blog animate-box" style="background-image: url(images/blog-3.jpg);">
										<a class="image-popup" href="#">
											<div class="prod-title">
												<span>Illustration</span>
												<h3>Don’t Just Stand There</h3>
												<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
											</div>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> -->
		</div>
	</div>
					<? include('web/footer.php'); ?>
</body>
</html>
