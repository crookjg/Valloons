var Quiz = Quiz || {};
Quiz.End = function() {}

var score;
var finalScore;
var gameid;
var totalQ;

Quiz.End.prototype = {
	init: function(total) {
		totalQ = total;
	},	
	preload: function() {
		score = this.registry.get('score');
		gameid = this.registry.get('gameid');
	},
	create: function() {
		// set background
		this.add.image(300, 300, 'bg');
	
		// set text for final score
		let style = {
			font: '24pt Arial',
			fill: '#fff',
			wordWrap: false
		};
		finalScore = (score / totalQ) * 100;
		scoreStr = 'Final Score: ' + score + ' / ' + totalQ + ' = ' + finalScore;
		this.finalScore = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 180, scoreStr, style).setOrigin(0.5);

		// Leaderboard text
		var ldrbrd = this.add.group();
		var ldrbrdTxt = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 135, 'Leaderboard', style).setOrigin(0.5);
		// send score to database
		this.sendScore();

		// add leaderboard info to group
		ldrbrd.add(ldrbrdTxt);
		ldrbrd.add(this.showLeaderboard());
	},
	sendScore: function() {
		$.ajax({
			type: 'post',
			url: '../post-results.php',
			data: { gameid, finalScore },
			async: false,
			success: function() {
				alert('Success.');
			},
			error: function(jqXHR, textStatus, error) {
				alert('Could not add results to database.');
			}
		});
	},
	showLeaderboard: function() {
		var leaderboardData;

		$.ajax({
			type: 'post',
			url: '../get-leaderboard-data.php',
			data: { gameid },
			async: false,
			success: function(data) {
				leaderboardData = JSON.parse(data);
			},
			error: function(jqXHR, textStatus, error) {
				leaderboardData = "No Data";
			}
		});

		var ldrbrd = this.add.group();

		let count = 1;
		let startY = -90;
		let style = {
			font: '24pt Arial',
			fill: '#fff',
			wordWrap: false
		};

		for (let i = 0; i < leaderboardData['leaders'].length; i++) {
			let place = count + '. ' + leaderboardData['leaders'][i];
			ldrbrd.add(this.add.text(this.cameras.main.centerX, this.cameras.main.centerY + startY, place, style).setOrigin(0.5));
		}

		return ldrbrd;
	}
	
}
