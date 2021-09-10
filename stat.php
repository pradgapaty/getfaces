<!DOCTYPE html>
<?php 
require_once "web/main.php"; 
require_once("/var/www/html/ParsePhoto/Helpers/LoaderHelper.php");

$parseThispersonClassInst = new ParseThisperson();
$parseAnalyzeClassInst = new ParseAnalyze();
?>
<head>
	<style>
	div.section_header {
	    padding: 3px 6px 3px 6px;
	    background-color: #8E9CB2;
	    color: #FFFFFF;
	    font-weight: bold;
	    font-size: 112%;
	    text-align: center;
	}
	</style>
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
								<?php $page = "stat"; include("web/menu.php"); ?>
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
						<h2>Project statistic</h2>
					</div>
				</div>
			</div>
<div class="row row-bottom-padded-lg">
<div class="col-md-12 animate-box fadeInUp animated">
					<div class="section_header">
						<div id="changes">Photo statistic</div>
					</div>
					<ul>
						<li>
							<p>
								<b>Total photos:
								<?php
									$total = $parseThispersonClassInst->getTotalRecords();
									echo $total;
								?>
								</b>
							</p>
						</li>
						<li>
							<p>
								<b>Waiting for analyze photos:
								<?php
									$waiting = $parseThispersonClassInst->getReadyRecords();
									echo $waiting;
								?>
								</b>
							</p>
						</li>
						<li>
							<p>
								<b>Analyzed photos:
								<?php
									$ready = $parseThispersonClassInst->getParsedRecords();
									echo $ready;
								?>
								</b>
							</p>
						</li>
					</ul>





					
					<div class="section_header">
						<div id="changes">Photo analyze</div>
					</div>
						<ul>
						<li>
							<p>
								<b>Active analyzators:
								<?php
									$count = $parseAnalyzeClassInst->getActiveAnalyzeCount();
									echo $count;
								?>
								</b>
							</p>
						</li>
					</ul>
</div>



		</div>
	</div>
					<? include('web/footer.php'); ?>
</body>
</html>
