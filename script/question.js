var Quiz = Quiz || {};
Quiz.Question = function() {}

var popSound;
var player;
var cursors;
var balloons;
var darts;
var dartTime;
var cursors;
var currQ;
var totalQ;
var scoreStr;
var scoreTxt;
var ansTxtX;
var ansTxtY;
var that;

Quiz.Question.prototype = {
	init: function(currQIndex) {
		ansTxtX = this.cameras.main.centerX;
		ansTxtY = this.cameras.main.centerY;
		that = this;
	},
	preload: function() {
		// load questions from registry
		this.data = this.registry.get('questions');
		// load balloons array & balloons array length from registry
		this.balloons = this.registry.get('balloons');
		this.numBalloons = this.registry.get('num_balloons');

		// balloon popping sound
		popSound = this.registry.get('popSound');
	},
	create: function() {
		// set background
		this.add.image(300, 300, 'bg');

		// set world bounds
		this.physics.world.setBounds(0, this.cameras.main.centerY + 200, 600, 100, true, true, false, true);
				
		// create falling balloons group
		balloons = this.add.group();
		balloons.enableBody = true;
		balloons.physicsBodyType = Phaser.Physics.ARCADE;

		// create shooting darts group
		darts = this.add.group();
		darts.enableBody = true;
		darts.physicsBodyType = Phaser.Physics.ARCADE;

		// create player & moving dart
		player = this.createPlayer();

		// create cursor keys for moving
 		cursors = this.input.keyboard.createCursorKeys();

		// show current question
		currQ = this.showQuestion(this.registry.get('currQIndex'));
		totalQ = this.listQuestions();

		// create score text
		this.showScore();

		// create exit button
		this.showExitButton();

		// set dartTimes so you can only fire a dart in intervals.
		dartTime = this.time.now + 300;

		// create balloons with answers
		for (let i = 0; i < this.numAns; i++) {
			// create answer container & return it here if it's active
			if (this.data.questions[this.registry.get('currQIndex')].choices[i].active == 1) {
				var choice = this.createAnswer(i);
				var balloon = balloons.add(choice);	// add container to balloons group
			}
		}

		// collide the balloon with the world bound
 		this.physics.world.on('worldbounds', this.groundHit);
	},
	update: function() {
		// keep score updated
		currScore = this.registry.get('score');

		// capture player movement
		if (cursors.left.isDown) {
			player.setVelocityX(-160);
		} else if (cursors.right.isDown) {
			player.setVelocityX(160);
		} else {
			player.setVelocityX(0);
		}

		// if space is pressed & a dart can be fired, fire it.
		if (cursors.space.isDown && this.time.now > dartTime) {
			let newDart = this.fireDart();
			darts.add(newDart);
		}

		// collide the dart with a balloon
		this.physics.add.collider(balloons, darts, this.balloonPop, null, this);

//		if (balloons.children.entries.length == 0)
//			console.log(this.registry.get('currQIndex'));
//			this.scene.start('answer', this.registry.get('currQIndex'));
	},
	// component functions
	createPlayer: function() {
		var playerDart = this.physics.add.sprite(this.cameras.main.centerX, this.cameras.main.centerY + 200, 'dart').setOrigin(0.5, 1).setScale(0.15);
		playerDart.setCollideWorldBounds(true);

		return playerDart;		
	},
	showScore: function() {
		var style = {
			font: '20pt Arial',
			fill: '#000',
			wordWrap: false,
			align: 'right'
		};
		var total = this.getTotalScore();
		scoreStr = 'Score: ' + this.registry.get('score') + ' / ' + total;
		scoreTxt = this.add.text(0, this.cameras.main.height - 30, scoreStr, style);

	},
	updateScore: function(newScore) {
		var style = {
			font: '20pt Arial',
			fill: '#000',
			wordWrap: false,
			align: 'right'
		};
		var total = this.getTotalScore();
		scoreTxt.text = 'Score: ' + newScore + ' / ' + total;
	},
	showExitButton: function() {
		var context = { game:this.game };
		var button = this.add.text(this.cameras.main.width - 55, this.cameras.main.height - 20, 'Exit Game')
			.setOrigin(0.5)
			.setPadding(10)
			.setStyle({backgroundColor: '#f84bf6', color: '#fff', borderRadius: '25px'})
			.setInteractive({ useHandCursor: true })
			.on('pointerdown', function() {
				that.scene.start('end');
			})
			.on('pointerover', () => button.setStyle({ backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px' }))
			.on('pointerout', () => button.setStyle({ backgroundColor: '#f84bf6', color: '#fff', borderRadius: '25px' }));
	},
	showQuestion: function(currIndex) {
		var currQuestion = this.getQuestion(currIndex);
		var questionTitle = this.add.text(this.cameras.main.centerX, 20, currQuestion, {
			font: '24pt Arial',
			fill: '#000',
			wordWrap: true,
			wordWrapWidth: 600,
			align: 'left',
			backgroundColor: 'rgba(255, 255, 255, 0.5)'
		}).setOrigin(0.5);
	
		this.numAns = this.getNumAnswers(currIndex);
		this.questionPoints = this.data.questions[currIndex].points;
	},
	createAnswer: function(index) {
		let max = 565;	// max (game width - balloon width w/ padding)
		let min = 25;	// min (0 + balloon width w/ padding
		var rand = Math.floor(Math.random() * min);	// random number between 0 & 25
		var randX = Math.floor(Math.random() * (max - min) + rand);	// random x
		var randY = (Math.floor(Math.random() * 100 + 50) * -1);	// random y
	
		var container = this.add.container(randX, randY);	// create container at random (x, y)
		var color = this.balloons[Math.floor(Math.random() * this.numBalloons)]; 	// choose random balloon color from 4 possibilities

		var balloon = this.add.image(43.5, 20, color);
		container.add(balloon);	// add balloon image to container

		var style = {
			font: '20pt Arial',
			fill: '#000'
		};
		var txt = this.add.text(0, 0, this.data.questions[this.registry.get('currQIndex')].choices[index].answer, style);
		txt.setX(txt.originX + 43.5 - (txt.width / 2));	// center text
	
		container.add(txt);	// add answer text to container
		container.setScale(0.75);	// scale balloon & answer

		// enable gameObject data
		container.setData('correct', this.data.questions[this.registry.get('currQIndex')].choices[index].correct);

		// enable gravity on each balloon so it falls
		this.physics.world.enable(container);
		container.body.setCollideWorldBounds(true);
		container.body.onWorldBounds = true;
		// set gravity rate so each balloon falls at a different pace
		container.body.setGravityY(1.5 * Math.random());

		return container;
	},
	fireDart: function() {
		let projectile = this.physics.add.sprite(player.x, player.y, 'dart').setOrigin(0.5, 1).setScale(0.15);
		projectile.body.gameObject.name = 'projectile';

		if (projectile.body != undefined) {
			projectile.setVelocityY(-200);
			dartTime = this.time.now + 500;
		}

		return projectile;
	},
	balloonPop: function(balloon, dart) {
		// find out if popped balloon was correct answer
		var correct = balloon.getData('correct');
		// pop the balloon & the dart
		balloons.remove(balloon, true, true);
		dart.destroy();
		// play the popping sound
		popSound.play();

		// get the current score & number of points the answer was worth
		var currScore = this.registry.get('score');
		var worth = this.questionPoints / this.numCorrect;

		if (correct == 1 && currScore >= 0 + worth) {
			currScore -= worth;
		} else if (correct == 1) {
			currScore = 0;
		}

		this.registry.set('score', parseFloat(currScore.toFixed(1)));
		this.updateScore(parseFloat(currScore.toFixed(1)));
	},
	groundHit: function(body) {
		// pull the game object out of the body.
		var container = body.gameObject;
		// check if the answer is correct or not
		var correct = container.getData('correct');
		// play sound
		popSound.play();

		// get current score & number of points answer is worth
		var currScore = that.registry.get('score');
		var worth;
		if (correct == 1)
			worth = that.questionPoints / that.numCorrect;
		else
			worth = that.questionPoints / that.numWrong;

		if (correct == 1)
			currScore += worth;
		else if (correct == 0 && currScore >= currScore - worth)
			currScore -= worth;
		else
			currScore = 0;

		that.registry.set('score', parseFloat(currScore.toFixed(1)));
		that.updateScore(parseFloat(currScore.toFixed(1)));

		// destroy container (balloon & text)
		//container.destroy();
		balloons.remove(container, true, true);
	},
	// helper functions
	getTotalScore: function() {
		var total = 0;
		for (var i = 0; i < this.data.questions.length; i++) {
			if (this.data.questions[i].active == 1)
				total += this.data.questions[i].points;
		}
		return total;
	},
	getQuestion: function(questionIndex) {
		if (this.data.questions[questionIndex].active == 1) {
			return this.data.questions[questionIndex].question;
		}
		else {
			this.registry.set('currQIndex', this.registry.get('currQIndex')++);
			this.scene.start('question');
		}
	},
	listQuestions: function() {
		var numQ = 0;
		for (var i = 0; i < this.data.questions.length; i++) {
			if (this.data.questions[i].active == 1)
				numQ++;
		}
		return numQ;
	},
	getNumAnswers: function(questionIndex) {
		var numA = 0;
		this.numCorrect = 0;
		this.numWrong = 0;
		for (var i = 0; i < this.data.questions[questionIndex].choices.length; i++) {
			if (this.data.questions[questionIndex].choices[i].active == 1)
				numA++;
			if (this.data.questions[questionIndex].choices[i].correct == 1)
				this.numCorrect++;
			else
				this.numWrong++;
		}
		return numA;
	}
}
