import Phaser from 'phaser';

var Quiz = Quiz || {};

const config = {
	type: Phaser.AUTO,
	width: 600,
	height: 600,
	backgroundColor: '#36b2f2',
	autoCenter: true,
	physics: {
		default: 'arcade',
		arcade: {
			gravity: { y: 200 }
		}
	},
	scene: {
		preload: preload,
		create: create
	}
};

Quiz.game = new Phaser.Game(config);
Quiz.game.state.add('boot', Quiz.Boot);
Quiz.game.state.add('intro', Quiz.Intro);
Quiz.game.state.add('question', Quiz.Question);
Quiz.game.state.add('answer', Quiz.Answer);
Quiz.game.state.add('end', Quiz.End);
Quiz.game.state.start('boot');

