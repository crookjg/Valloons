var Quiz = Quiz || {};
Quiz.Question = function() {}

Quiz.Question.prototype = {
	init: function() {
		this.data = this.registry.get('questions');
		console.log(this);
		console.log(this.data);
	},
	preload: function() {},
	create: function() {
		this.add.image(300, 300, 'bg');

		this.balloons = this.registry.get('balloons');
		this.numBalloons = this.registry.get('num_balloons');

		var question = this.showQuestion(this.registry.get('currQIndex'));
		var totalQuestions = this.listQuestions();

		this.registry.set('numQuestions', totalQuestions);
		this.showScore(this.registry.get('score'));
		this.showExitButton();

		//let balloon = this.physics.add.sprite(this.sys.game.config.width/2, 0, 'red');
		//balloon.setGravityY(5);

		this.dartTime = this.time.now + 3000;

		this.createDart();
	},
	update: function() {
		if (this.cursors.left.isDown) {
			this.player.setVelocityX(-160);
		} else if (this.cursors.right.isDown) {
			this.player.setVelocityX(160);
		} else {
			this.player.setVelocityX(0);
		}

		if (this.cursors.space.isDown && this.time.now > this.dartTime) {
			this.fireDart();
		}
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
	
		var numAns = this.getNumAnswers(currIndex);
		this.addChoices(this.data.questions[currIndex].choices);
	},
	getQuestion: function(questionIndex) {
		if (this.data.questions[questionIndex].active == 1)
			return this.data.questions[questionIndex].question;
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
		for (var i = 0; i < this.data.questions[questionIndex].choices.length; i++) {
			if (this.data.questions[questionIndex].choices[i].active == 1)
				numA++;
		}
		return numA;
	},
	getTotalScore: function() {
		var total = 0;
		for (var i = 0; i < this.data.questions.length; i++) {
			if (this.data.questions[i].active == 1)
				total += this.data.questions[i].points;
		}
		return total;
	},
	showScore: function(score) {
		var style = {
			font: '20pt Arial',
			fill: '#000',
			wordWrap: false,
			align: 'right'
		};
		var total = this.getTotalScore();
		var textContent = 'Score: ' + score + ' / ' + total;
		var textE1 = this.add.text(0, this.cameras.main.height - 30, textContent, style);
	},
	showExitButton: function() {
		var context = { game:this.game };
		var that = this;
		var button = this.add.text(this.cameras.main.width - 55, this.cameras.main.height - 20, 'Exit Game')
			.setOrigin(0.5)
			.setPadding(10)
			.setStyle({backgroundColor: '#f84bf6', color: '#fff', borderRadius: '25px'})
			.setInteractive({ useHandCursor: true })
			.on('pointerdown', function() {
				that.scene.start('intro');
			})
			.on('pointerover', () => button.setStyle({ backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px' }))
			.on('pointerout', () => button.setStyle({ backgroundColor: '#f84bf6', color: '#fff', borderRadius: '25px' }))			
		return button;
	},
	createDart: function() {
		this.player = this.physics.add.sprite(this.cameras.main.centerX, this.cameras.main.centerY + 250, 'dart').setOrigin(0.5, 1).setScale(0.33);
		this.player.setCollideWorldBounds(true);

		this.cursors = this.input.keyboard.createCursorKeys();
	},
	fireDart: function() {
		let projectile = this.physics.add.sprite(this.player.x, this.player.y, 'dart').setOrigin(0.5, 1).setScale(0.33);
		projectile.body.gameObject.name = 'projectile';

		if (projectile.body != undefined) {
			projectile.setVelocityY(-200);
			this.dartTime = this.time.now + 500;
			if (projectile.y < 0) {
				projectile.destroy();
			}
		}
	},
	addChoices: function(choices) {
		var groupAns = this.add.group();

		for (var i = 0; i < choices.length; i++) {
			var correct = choices[i].correct;
			let grp = this.addChoiceContainer(choices[i].answer, correct);
			groupAns.add(grp);
			blnTime = 0;
		}
	},
	addChoiceContainer: function(answer, correct) {
		let max = 565;
		let min = 25;
		var rand = Math.floor(Math.random() * min);
		var randX = Math.floor(Math.random() * (max - min) + rand);

		var randY = (Math.floor(Math.random() * 100 + 150) * -1);

//		var container = this.add.container(randX, 150);
		var container = this.add.container(randX, randY);
		var color = this.balloons[Math.floor(Math.random() * this.numBalloons)]; 

		var balloon = this.add.image(43.5, 20, color);
		var style = {
			font: '20pt Arial',
			fill: '#000'
		};

		container.add(balloon);

		var txt = this.add.text(0, 0, answer, style);
		txt.setX(txt.originX + 43.5 - (txt.width / 2));

		container.add(txt);
		container.setScale(0.75);
		
		this.physics.world.enable(container);
		container.body.setGravityY(1.25 + Math.random());
//		container.body.setGravityY(0);

		return container;
	}

}
