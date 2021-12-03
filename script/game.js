var Quiz = Quiz || {};

const config = {
	type: Phaser.AUTO,
	width: 900,
	height: 600,
	backgroundColor: '#36b2f2',
	autoCenter: Phaser.Scale.CENTER_HORIZONTALLY,
	physics: {
		default: 'arcade',
		arcade: { debug: false }
	}
};

Quiz.game = new Phaser.Game(config);
Quiz.game.scene.add('boot', Quiz.Boot);
Quiz.game.scene.add('directions', Quiz.Directions);
Quiz.game.scene.add('intro', Quiz.Intro);
Quiz.game.scene.add('question', Quiz.Question);
Quiz.game.scene.add('end', Quiz.End);

Quiz.game.scene.start('boot');

