var Quiz = Quiz || {};
Quiz.Intro = function() {}

Quiz.Intro.prototype = {
	preload: function() {
		// set game score to 0
		this.registry.set('score', 0);
	},
	create: function() {
		// set background
		this.add.image(this.cameras.main.centerX, this.cameras.main.centerY, 'bg');

		// set text group
		var introGrp = this.add.group();
		var start = this.createStartButton();
		var hdrs = this.createTextHeaders();
		introGrp.add(hdrs);
		introGrp.add(start);
	},
	createStartButton: function() {
		var context = { game:this.game };
		var that = this;
		var button = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY, 'Start Game')
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
		var welcome = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 90, texts[0], style).setOrigin(0.5);
		var start = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 45, texts[1], style).setOrigin(0.5);
		grp.add(welcome);
		grp.add(start);
		return grp;
	}
}

