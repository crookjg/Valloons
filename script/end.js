var Quiz = Quiz || {};
Quiz.End = function() {}

var score;
var gameid;

Quiz.End.prototype = {
	preload: function() {
		score = this.registry.get('score');
		gameid = this.registry.get('gameid');
	},
	create: function() {
		// set background
		this.add.image(this.cameras.main.centerX, this.cameras.main.centerY, 'bg');
	
		// set text for final score
		let style = {
			font: '24pt Arial',
			fill: '#fff',
			wordWrap: false
		};
		scoreStr = 'Final Score: ' + score;
		this.finalScore = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 180, scoreStr, style).setOrigin(0.5);

		// add exit button to dashboard
		this.createDashboardButton();

		// Leaderboard text
		var ldrbrd = this.add.group();
		var ldrbrdTxt = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 90, 'Leaderboard', style).setOrigin(0.5);
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
			data: { gameid, score },
			async: false,
			success: function() {
//				alert('Success.');
			},
			error: function(jqXHR, textStatus, error) {
//				alert('Could not add results to database.');
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
		let startY = -45;
		let style = {
			font: '24pt Arial',
			fill: '#fff',
			wordWrap: false
		};
		
		var leadLen = (leaderboardData.length > 8) ? 8 : leaderboardData.length;
		
		for (let i = 0; i < leadLen; i++) {
			let place = count + '. ' + leaderboardData[i];
			ldrbrd.add(this.add.text(this.cameras.main.centerX, this.cameras.main.centerY + startY, place, style).setOrigin(0.5));
			startY += 45;
			count++;
		}

		return ldrbrd;
	},
	createDashboardButton: function() {
		var context = { game:this.game };
		var that = this;
		var button = this.add.text(this.cameras.main.centerX, this.cameras.main.centerY - 135, 'Return to Dashboard')
			.setOrigin(0.5)
			.setPadding(10)
			.setStyle({backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px'})
			.setInteractive({ useHandCursor: true })
			.on('pointerdown', function() {
				location.href="dashboard.php";
			})
			.on('pointerover', () => button.setStyle({ backgroundColor: '#f84bf6', color: '#fff', borderRadius: '25px' }))
			.on('pointerout', () => button.setStyle({ backgroundColor: '#26ee2b', color: '#000', borderRadius: '25px' }));
	}

	
}
