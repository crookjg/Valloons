var quiz = Quiz || {};
Quiz.End = function() {};

var score;

Quiz.End.prototype = {
	preload: function() {
		score = this.registry.get('score');
	},
	create: function() {
		console.log(this);
	},
	update: function() {}
}
