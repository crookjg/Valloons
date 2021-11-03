var Quiz = Quiz || {};
Quiz.Boot = function() {}

Quiz.Boot.prototype = {
	init: function() {
		this.currQIndex = 0;
		this.score = 0;
	},
	preload: function() {
		// load all images from media folder needed for game
		this.load.image('logo', '/media/logo.png');
		this.load.image('bg', '/media/ValloonsBG.jpg');
		this.load.image('red', '/media/RedBalloon.png');
		this.load.image('blue', '/media/BlueBalloon.png');
		this.load.image('green', '/media/GreenBalloon.png');
		this.load.image('pink', '/media/PinkBalloon.png');
		this.load.image('dart', '/media/CartoonDart.png');
		this.load.audio('pop', ['/media/balloonPop.wav']);
		
		this.balloons = ['red', 'blue', 'green', 'pink'];
		this.num_balloons = 4;

		this.registry.set('currQIndex', this.currQIndex);
		this.registry.set('score', this.score);
		this.registry.set('balloons', this.balloons);
		this.registry.set('num_balloons', this.num_balloons);

		var gameid = $('#gameid').text();
		var gameData;
		$.ajax({
			type: 'post',
			url: '../get-game-data.php',
			data: { gameid },
			async: false,
			success: function(data) {
				//console.log(data);
				gameData = JSON.parse(data);
				//console.log(gameData);
			},
			error: function(jqXHR, textStatus, error) {
				//console.log(textStatus, error);
				alert('Game has stopped working. Please go back to the dashboard.');
			}
		});
		
		// load questions from json formatting
		this.registry.set('questions', gameData);
	},
	create: function() {
		this.popSound = this.sound.add('pop', {loop: false} );
		this.scene.start('intro');
	}
}

