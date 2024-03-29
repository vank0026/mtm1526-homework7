<?php

require_once 'includes/filter-wrapper.php';
require_once 'includes/functions.php';


$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (empty($id)) {
	header('Location: index.php');	// this is if there is no page
	exit;	//stop the PHP right here and immediately redirect the user, ONLY connect to the database if there is a statement to tmake
}

//	**********************
//			By using this format, MySQL is plrotected from a type of common hacking
//	**********************

require_once 'includes/db.php';

		// 	prepare() allows execution of sql commands with user input
$sql = $db->prepare('
SELECT id, park_name, longitude, latitude, address, rate_count, rate_total
FROM parks
WHERE id = :id
');

$sql->bindValue(':id', $id, PDO::PARAM_INT);

// perform the sql query on the database
$sql->execute();
// get the results and stores them in a variable ($results) - fetch gets a single result, fetch all gets all possible results
$results = $sql->fetch();

if (empty($results)) {
	header('Location: index.php');	// this is if there is no page
	exit;	//stop the PHP right here and immediately redirect the user, ONLY connect to the database if there is a statement to tmake
}

if ($results['rate_count'] > 0) {
	$rating = round($results['rate_total'] / $results['rate_count']);
	} else {
	$rating = 0;
	}

$cookie = get_rate_cookie();

?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $results['park_name']; ?></title>
<link href="css/parks.css" rel="stylesheet">

</head>
<body>
	<h1>Comunity Gardens List</h1>
		<div id="list">
		<h2><?php echo $results['park_name']; ?></h2>

		<p>Average Park Rating:<meter value="<?php echo $rating; ?>" min="0" max="5"><?php echo $rating; ?> out of 5</meter></p>
		<p>Longitude: <?php  echo $results['longitude']; ?></p>
		<p>Latitude: <?php  echo $results['latitude']; ?></p>
		<p>Address: <?php  echo $results['address']; ?></p>
        
				<span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
                    <meta itemprop="longitude" content="<?php echo $results['longitude']; ?>">
                    <meta itemprop="latitude" content="<?php echo $results['latitude']; ?>">
                </span>
        
    
    <?php if (isset($cookie[$id])) : ?>

<h2>You Have Rated This Park:</h2>
    <ol class="rater rater-usable">
		<?php for ($i = 1; $i <= 5; $i++) : ?>
        <?php $class = ($i <= $cookie[$id]) ? 'is-rated' : ''; ?>
            <li class="rater-level <?php echo $class; ?>">★</li>
        <?php endfor; ?>
    </ol>
        <p><a href="index.php">Return Home</a></p>

<?php else : ?>

<h2>Rate This Park:</h2>
    <ol class="rater rater-usable">
    <?php for ($i = 1; $i <= 5; $i++) : ?>
        <li class="rater-level"><a href="rate.php?id=<?php echo $results['id']; ?>&rate=<?php echo $i; ?>">★</a></li>
    <?php endfor; ?>
    </ol>
        <p><a href="index.php">Return Home Without Rating</a></p>
<?php endif; ?>
    
	</div>

<div id="map"></div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCOSF6EUJHi28FLeCSkKsQsG1gtn4vRkN4&sensor=false"></script>
	<script src="js/park-finder.js"></script>
</body>
</html>
