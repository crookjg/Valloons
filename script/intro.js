var Quiz = Quiz || {};
Quiz.Intro = function() {}

Quiz.Intro.prototype = {
	preload: function() {
		this.registry.set('score', 0);
	},
	create: function() {
		this.add.image(300, 300, 'bg');
		var intoGroup = this.add.group();
		var btnGroup = this.createButton();
		var txtGroup = this.createTextHeaders();
		intoGroup.add(btnGroup);
		intoGroup.add(txtGroup);
	},
	createButton: function() {
		var context = { game:this.game };
		var that = this;
		var button = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY, 'Start Game')
			.setOrigin(0.5)
			.setPadding(10)
			.setStyle({backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px'})
			.setInteractive({ useHandCursor: true })
			.on('pointerdown', function() {
				that.scene.start('question', 0);
			})
			.on('pointerover', () => button.setStyle({ backgroundColor: '#f84bf6', color: '#fff', borderRadius: '25px' }))
			.on('pointerout', () => button.setStyle({ backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px' }))			
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

