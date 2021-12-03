var Quiz = Quiz || {};
Quiz.Directions = function() {};

Quiz.Directions.prototype = {
	preload: function() {},
	create: function() {
		// set background
		this.add.image(this.cameras.main.centerX, this.cameras.main.centerY, 'bg');

		// set directions text group
		var dirGrp = this.add.group();
		var next = this.createNextButton();
		var dirs = this.createDirections();
		dirGrp.add(dirs);
		dirGrp.add(next);
	},
	createNextButton: function() {
		var context = { game:this.game };
		var that = this;
		var button = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY, 'Next')
			.setOrigin(0.5)
			.setPadding(10)
			.setStyle({backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px'})
			.setInteractive({ useHandCursor: true })
			.on('pointerdown', function() {
				that.scene.start('intro');
			})
			.on('pointerover', () => button.setStyle({ backgroundColor: '#f84bf6', color: '#fff', borderRadius: '25px' }))
			.on('pointerout', () => button.setStyle({ backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px' }));
		return button;
	},
	createDirections: function() {
		var texts = ['1. Use Left and Right keys to move player dart.', '2. Use space to fire darts.', '3. Pop the wrong answers.', '4. Let the correct answers hit the bottom.'];
		var grp = this.add.group();
		var style = { font: '20pt Arial', wordWrap: true, align: 'left', color: '#fff' };
		var dir = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 180, texts[0], style).setOrigin(0.5);
		var dir1 = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 135, texts[1], style).setOrigin(0.5);
		var dir2 = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 90, texts[2], style).setOrigin(0.5);
		var dir3 = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 45, texts[3], style).setOrigin(0.5);
		grp.add(dir);
		grp.add(dir1);
		grp.add(dir2);
		grp.add(dir3);
		return grp;
	}
}
