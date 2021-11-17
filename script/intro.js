var Quiz = Quiz || {};
Quiz.Intro = function() {}

Quiz.Intro.prototype = {
	preload: function() {
		this.registry.set('score', 0);
	},
	create: function() {
		this.add.image(300, 300, 'bg');
		var introGrp = this.add.group();
		var start = this.createStartButton();
		var hdrs = this.createTextHeaders();
		var dirs = this.createDirections();
		introGrp.add(hdrs);
		introGrp.add(dirs);
		introGrp.add(start);
	},
	createStartButton: function() {
		var context = { game:this.game };
		var that = this;
		var button = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY + 95, 'Start Game')
			.setOrigin(0.5)
			.setPadding(10)
			.setStyle({backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px'})
			.setInteractive({ useHandCursor: true })
			.on('pointerdown', function() {
				that.scene.start('question');
			})
			.on('pointerover', () => button.setStyle({ backgroundColor: '#f84bf6', color: '#fff', borderRadius: '25px' }))
			.on('pointerout', () => button.setStyle({ backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px' }));
		return button;
	},
	createTextHeaders: function() {
		var texts = ['Welcome to Valloons!', 'Press Start to begin the game!'];
		var grp = this.add.group();
		var style = { font: '32pt Arial', wordWrap: false, align: 'left', color: '#fff' };
		var welcome = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 175, texts[0], style).setOrigin(0.5);
		var start = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 130, texts[1], style).setOrigin(0.5);
		grp.add(welcome);
		grp.add(start);
		return grp;
	},
	createDirections: function() {
		var texts = ['1. Use Left and Right keys to move player dart.', '2. Use space to fire darts.', '3. Pop the wrong answers.', '4. Let the correct answers hit the bottom.'];
		var grp = this.add.group();
		var style = { font: '20pt Arial', wordWrap: true, align: 'left', color: '#fff' };
		var dir = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 85, texts[0], style).setOrigin(0.5);
		var dir1 = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 40, texts[1], style).setOrigin(0.5);
		var dir2 = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY + 5, texts[2], style).setOrigin(0.5);
		var dir3 = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY + 50, texts[3], style).setOrigin(0.5);
		grp.add(dir);
		grp.add(dir1);
		grp.add(dir2);
		grp.add(dir3);
		return grp;
	}
}

