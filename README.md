# Valloons

A Phaser.js based game / web application where teachers can add questions and send the game link to studnets who can play the game and receive a score.

# File Layout
bootstrap -> contains min.css files for easy css customization  
script -> contains game JS files
<ul>
  <li>game.js</li>
  <li>boot.js</li>
  <li>directions.js</li>
  <li>intro.js</li>
  <li>question.js</li>
  <li>end.js</li>
</ul>
media -> images and audio files  
style -> style.css for added css customization  
php files in main folder include the front-end and back-end of the website files and layout.
<ul>
  <li>config.php -> configuration file connecting php to MySQL database</li>
  <li>index.php -> landing page for login & registration</li>
  <li>logout.php -> logs a user out</li>
  <li>dashboard -> dashboard for all users</li>
  <li>game.php -> calls game JS files</li>
  <li>template.php -> template php file</li>
  <li>verify.php -> empty (should verify users when registering / email verification)</li>
  <li>profile.php -> users profile information that can be changed</li>
  <li>edit-game.php -> teachers & admins can edit their games on this page</li>
  <li>get-game-data.php -> called by script/boot.js file to get game data in JSON format from database</li>
  <li>get-leaderboard-data.php -> called by script/end.js file to get leaderboard data in JSON format from database</li>
  <li>header.php -> header for website, called by other php files</li>
  <li>post-results.php -> called by script/end.js file to post game results to database</li>
</ul>
    
