var Quiz = Quiz || {};
Quiz.Boot = function() {}

Quiz.Boot.prototype = {
	init: function() {
		this.currentQuestionIndex = 0;
		this.score = 0;
		this.player;
		this.cursors;
		this.darts;
		this.shootBtn;
		this.pop;
		this.field;
		this.scoreStr = '';
		this.scoreTxt;
		this.chances;
		this.chancesTxt;
	},
	preload: function() {
		// load all images from media folder needed for game
		this.load.image('logo', '/media/logo.png');
		this.load.image('bg', '/media/ValloonsBG.jpg');
		this.load.image('red', '/media/RedBalloon.png');
		this.load.image('blue', '/media/BlueBalloon.png');
		this.load.image('green', '/media/GreenBalloon.png');
		this.load.image('pink', '/media/PinkBalloons.png');
		this.load.image('dart', '/media/CartoonDart.png');
		this.load.image('next', '/media/NextBtn.png');
	
		// load questions from json formatting
		this.load.json('questions', getQuestions());
		
		// start button to actually start the game
		this.load.spritesheet('start', 'media/StartBtn.png', 200, 90);
	},
	create: function() {
		this.add.image(300, 300, 'bg');
	
		//field = game.add.tileSprite(0, 0, 600, 600, 'bg');
		this.state.start('intro');
	}
}

