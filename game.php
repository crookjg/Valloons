<?php

session_start();

include ('config.php');

?>
<!DOCTYPE HTML>
<html>
<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style/style.css">
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/phaser@3.55.2/dist/phaser-arcade-physics.min.js"></script>
</head>
<body>
<?php
	include('header.php');
?>
	<main class="container">
<script type="text/JavaScript">
/*
	//dart group
	darts = game.add.group();
	darts.enableBody = true;
	dart.physcisBodyType = Phaser.Physics.ARCADE;
	darts.createMultiple(chances, 'dart');
	darts.setAll('anchor.x', 0.5);
	darts.setAll('anchor.y', 1);
	darts.setAll('outOfBoundsKill', true);
	darts.setAll('checkWorldBounds', true);

	//player
	player = game.add.sprite(400, 500, 'dart');
	player.anchor.setTo(0.5, 0.5);
	game.physics.enable(player, Phaser.Physics.ARCADE);

	//balloons
	choices = game.add.group();
	choices.enableBody = true;
	choice.physicsBodyType = Phaser.Physics.ARCADE;

	createBalloons();

	//current question
	questionTxt = game.add.text(10, 10, getCurrQuestion(), {font: '34px Arial', fill: '#000'});

	//scoring
	scoreStr = 'Score: ';
	scoreTxt = game.add.text(10, 590, scoreStr + score, {font: '34px Arial', fill: '#000'});

	//chances
	chancesTxt.add.group();
	game.add.text(game.world.width - 100, game.world.height - 10, 'Chances Left: ', {font: '34px Arial', fill: '#000'});

	//text
	stateTxt = game.add.text(game.world.centerX, game.world.centerY, ' ', {font: '60px Arial', fill: '#000'});
	stateTxt.anchor.setTo(0.5, 0.5);
	stateTxt.visible = false;

	for (var i = 0; i < chances; i++) {
		var dart = chances.create(game.world.width - 100 + (30 * i), 60, 'dart');
		dart.anchor.setTo(0.5, 0.5);
		dart.angle = 90;
		dart.alpha = 0.6;
	}

	//pop balloon
	pop = game.add.group();
	pop.createMultiple(choices, 'pop');
	pop.forEach(popBalloon, this);

	//controls
	cursors = game.input.keyboard.createCursorKeys();
	shootBtn = game.input.keyboard.addKey(Phaser.Keyboard.SPACEBAR);
	 */
}

function createBalloons() {

}

function popBalloon() {

}

function fall() {

}

function update() {

}

function collision(dart, balloon) {

}

function wrongChoiceFalls(balloon, choice) {

}

function shootDart() {

}

function end() {

}

function restart() {

}
</script>
	</main>
</body>
</html>
