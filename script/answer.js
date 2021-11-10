var Quiz = Quiz || {};
Quiz.Answer = function() {}

Quiz.Answer.prototype = {
	init: function(currQIndex) {
		currQIndex += 1;
		this.registry.set('currQIndex', currQIndex);
	},
	preload: function() {
		// load data
		this.data = this.registry.get('questions');
	},
	create: function() {
		var numQ = this.getNumQuestions();
		console.log(numQ);
		if (this.registry.get('currQIndex') >= numQ)
			this.scene.start('end');
		else {
			this.scene.start('question');
		}

	},
	getNumQuestions: function() {
		var count = 0;
		for (let i = 0; i < this.data.questions.length; i++) {
			if (this.data.questions[i].active == 1)
				count++;
		}
		return count;
	}
}

