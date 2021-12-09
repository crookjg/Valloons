# Valloons

A Phaser.js based game / web application where teachers can add questions and send the game link to studnets who can play the game and receive a score.

# File Layout
bootstrap -> contains min.css files for easy css customization
script -> contains game JS files
  game.js
  boot.js
  directions.js
  intro.js
  question.js
  end.js
media -> images and audio files
style -> style.css for added css customization
php files in main folder include the front-end and back-end of the website files and layout.
  config.php -> configuration file connecting php to MySQL database
  index.php -> landing page for login & registration
  logout.php -> logs a user out
  dashboard -> dashboard for all users
  game.php -> calls game JS files
  template.php -> template php file
  verify.php -> empty (should verify users when registering / email verification)
  profile.php -> users profile information that can be changed
  edit-game.php -> teachers & admins can edit their games on this page
  get-game-data.php -> called by script/boot.js file to get game data in JSON format from database
  get-leaderboard-data.php -> called by script/end.js file to get leaderboard data in JSON format from database
  header.php -> header for website, called by other php files
  post-results.php -> called by script/end.js file to post game results to database
  
  
