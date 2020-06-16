<?php
require('config.php');
require('model/init.php');

$stmt = $db->prepare("SELECT count(title) as count FROM skylinks WHERE 1");
if (!$stmt->execute()) {
    exit('Database error');
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$skylink_count = $row['count'];
$stmt = null;

?>
<html>
	<head>
		<title>DappDappGo search engine</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="author" content="colorlib.com">
		<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet" />
		<link href="static/css/main.css" rel="stylesheet" />
	</head>
	<body>
		<div class="s003" style="background-color: rgb(154, 159, 241);">
			
			<div style="position: absolute; margin-bottom: 300px; top: calc(50%/3);">
				<img src="static/images/duck_200.png" alt="duck" style="width: 200px; display: inline-block; vertical-align: bottom;"/> 
				<div style="display: inline-block; color: #FFF; font-size: 200%; margin-left: 10px;">
					Dapp<br>
					Dapp<br>
					Go
				</div>
			</div><br>
			<form action="search.php">
				<div class="inner-form">
					<!--
					<div class="input-field first-wrap">
						<div class="input-select">
							<select data-trigger="" name="choices-single-defaul">
								<option placeholder="">Category</option>
								<option>New Arrivals</option>
								<option>Sale</option>
								<option>Ladies</option>
								<option>Men</option>
								<option>Clothing</option>
								<option>Footwear</option>
								<option>Accessories</option>
							</select>
						</div>
					</div>
					-->
					<div class="input-field second-wrap">
						<input id="search" name="q" type="text" placeholder="Enter Keywords?" />
					</div>
					<div class="input-field third-wrap">
						<button class="btn-search" type="submit" style="background-color: #FF9500;">
							<svg class="svg-inline--fa fa-search fa-w-16" aria-hidden="true" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
								<path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
							</svg>
						</button>
					</div>
				</div>
			</form>
		</div>
		<footer style="font-family: 'Poppins', sans-serif; position: absolute; bottom: 0px; width: 100%; padding: 8px 20px; color: #FFF; background-color: rgba(0, 0, 0, 0.6);">
			<div style="float:left;">
				<a href="https://github.com/DaWe35/DappDappGo" style="color: #FFF; text-decoration: none;">Github</a> - 
				<a href="https://github.com/DaWe35/DappDappGo/tree/master/public_html/api" style="color: #FFF; text-decoration: none;">API</a>
			</div>
			<div style="float:right;">
				Indexed pages: <?= $skylink_count ?>
			</div>

		</footer>
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
