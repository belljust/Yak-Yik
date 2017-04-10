# Yak-Yik clones current "Yik-yak" website. (https://www.yikyak.com/home)

*.png --> raw image files used on webpage

resgister.php --> inital required php page from main.php for registration process. Layout of logout procedure which connects to the databse for confirmation. Using md5 hashing for storing and updating information.

main.php --> The only page of which the user interacts with. Using Restful API, all posts and updates are made form information gathered on this page. All inforamtion is sent via JQuery and Ajax calls in JSON format. The actual visualization to the user. Uses Google location API for calculating earth distances and radii.

connect2db.php --> Login php page. Layout of logout procedure which connects to the databse for confirmation. Using md5 hashing for storing and updating information.

*.css --> uses media queries for the application to be compatible with desktop, tablet and mobile devices. Utilizing responsive designs.
