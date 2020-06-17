<?php
require('config.php');
require('model/init.php');
include('model/converter.php');
$portal = 'https://siasky.net/';

if ($_GET['q'] == 'getrandom') {
	$query = "SELECT title, skypath, filename, description, `content-type`, `content-length` FROM skylinks WHERE title IS NOT NULL ORDER BY RAND() LIMIT 30";
	$stmt = $db->prepare($query);
	$exe = $stmt->execute();
} else {
	$query = "SELECT title, skypath, filename, description, `content-type`, `content-length` FROM skylinks WHERE title IS NOT NULL AND (MATCH(title, `filename`, content) AGAINST(? IN NATURAL LANGUAGE MODE) OR skypath = ?) LIMIT 100";
	$stmt = $db->prepare($query);
	$exe = $stmt->execute([$_GET['q'], $_GET['q']]);
}

if (!$exe) {
	exit('Database error');
} ?>

<!DOCTYPE html>
<html>
	<head>
		<title><?= htmlspecialchars($_GET['q']) ?> | DappDappGo</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="author" content="colorlib.com">
		<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet" />
		<link href="static/css/main.css" rel="stylesheet" />
		<style>
			a {
				color: #000;
				text-decoration: none;
			}
			h3.title {
				margin: 5px;
				font-size: 140%;
			}
			.info {
				font-size: 80%;
			}
			.url {
				color: #48BB78;
			}
			p.description {
				margin: 5px;
				font-size: 90%;
			}
			.result {
				margin: 30px;
				display: block;
			}
			#logo {
				display: inline-block;
				width: 155px;
				height: min-content;
			}
			.underdotted {
				text-decoration-line: underline;
				text-decoration-style: dotted;
			}
			.meta {
				background-color: #E2E8F0;
				padding: 2px 10px;
			}
			.info-favicon {
				height: 16px;
				vertical-align: middle;
			}
			.m-10-5 {
				margin: 5px 0px;
			}
		</style>
	</head>
	<body>
		<div class="s003" style="background-color: rgb(154, 159, 241); display: block;">
			<form action="search.php" style="margin: auto;">
				<div style="display: flex;">
					<a href="../">
						<img src="static/images/duck_200.png" alt="duck" id="logo" />
					</a>
					<div class="inner-form" style="height: min-content; margin-top: 10px; margin-left: 20px;">
						<div class="input-field second-wrap">
							<input id="search" name="q" type="text" placeholder="Enter Keywords?" value="<?= htmlspecialchars($_GET['q']) ?>" />
						</div>
						<div class="input-field third-wrap">
							<button class="btn-search" type="submit" style="background-color: #FF9500;">
								<svg class="svg-inline--fa fa-search fa-w-16" aria-hidden="true" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
									<path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
								</svg>
							</button>
						</div>
					</div>
				</div>
			</form>
			<div style="background-color: #F7FAFC; max-width: 790px; margin: auto; margin-top: 50px; border-radius: 10px; padding: 30px;"> <?php
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$title = !empty($row['title']) ? $row['title'] : $row['filename'];
					$type = str_replace('; charset=utf-8', '', $row['content-type']);
					?>
					<div class="result"> <?php
						if (substr($row['content-type'], 0, 5) == 'image') { ?>
							<div style="display: inline-block; margin-right: 10px; max-width: 200px; vertical-align: top;">
								<a href="<?= $portal . $row['skypath'] ?>">
									<img src="thumbnails/<?= $row['skypath'] ?>.jpg" alt="<?= $title ?>" />
								</a>
							</div>
							
							<div style="display: inline-block; max-width: 456px;"> <?php
						} ?>
								<a href="<?= $portal . $row['skypath'] ?>">
									<h3 class="title"> <?= $title ?> </h3>
								</a>
								<p class="info">
									<img class="info-favicon m-10-5" src="https://siasky.net/favicon-32x32.png" alt="Skynet favicon" />
									<span class="meta m-10-5"><?= $type ?></span> <?php
									if ($row['content-length'] > 1000000) { ?>
										<span class="meta"><?= formatBytes($row['content-length']) ?></span> <?php
									} ?>
									<span class="url m-10-5"><?= $row['skypath'] ?></span>
								</p>
								<p class="description"> <?= $row['description'] ?> </p></div><?php
						if (substr($row['content-type'], 0, 5) == 'image') { ?>
							</div> <?php
						} ?> <?php

					$inwhile = true;
				}
				$stmt = null;
				if (!isset($inwhile)) { ?>
					<div style="text-align: center;">
						<p>Sorry, your search did not match any documents</p>
						<img src="static/images/404-duck.svg" alt="duck">
						<p>You know what? <a href="/search.php?q=getrandom" class="underdotted">Get some random results!</a></p>
					</div> <?php
				} ?>
			</div>
		</div>
		<script src="static/js/extention/choices.js"></script>
		<script>
			const choices = new Choices('[data-trigger]',
			{
				searchEnabled: false,
				itemSelectText: '',
			});

		</script>
	</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>