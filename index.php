<html>
	<header>
		<link rel="stylesheet" type="text/css" href="css/main.css">
	</header>
</html>
<?php
//Configuration for our PHP server
set_time_limit(0);
ini_set('default_socket_timeout', 300);
session_start();

//Make Constants using define.
define('clientID', 'e890dea8c1cb4192a569f689cfb902ac');
define('clientSecret', '60c69e31b40f4860b81d07f5a83a54c5');
define('redirectURI', 'http://localhost/Diego/index.php');
define('ImageDirectory', 'pics/');

 
  // function that is going to connet to insta
	function connectToInstagram($url){
		$ch = curl_init();
		
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 2,
			));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	// function to get userID cause username doesnt allow us to get pics
	function getUserID($userName){
		$url = 'https://api.instagram.com/v1/users/search?q=' .$userName. '&client_id='.clientID;
		$instagramInfo = connectToInstagram($url);
		$results = json_decode($instagramInfo, true);
		
		return $results['data']['0']['id'];
	}
	// function to print out images on screen
	function printImages($userID){
		$url = 'https://api.instagram.com/v1/users/' .$userID. '/media/recent?client_id=' .clientID. '&count=5';
		$instagramInfo = connectToInstagram($url);
		$results = json_decode($instagramInfo, true);
		//parse through the information one by one 
		foreach ($results['data'] as $items){
			// going to go through all of my results and give myself back the url of those pictures because we want to save it in the PHP server
			$image_url = $items['images']['low_resolution']['url'];
			
			echo '<img src=" ' .$image_url. ' "/><br/>';
			// calling a function to save that $image_url
			savePictures($image_url);
		}
	}
	// function to save images to server 
	function savePictures($image_url){
		//echo $image_url .'<br>';
		$filename = basename($image_url);// the filename is what we are storing. Basename is the PHP bult in the method that we ere using to store $image_url
		//echo $filename . '<br>';
		
		// making sure that the image doesnt exist in the storage
		$destination = ImageDirectory . $filename;
		// goes and grabs an imagefile and stores it into our server 
		file_put_contents($destination, file_get_contents($image_url));
	}
if (isset($_GET['code'])){
	$code = ($_GET['code']);
	$url = 'https://api.instagram.com/oauth/access_token';
	$access_token_settings = array('client_id' => clientID,
									'client_secret' => clientSecret,
									'grant_type' => 'authorization_code',
									'redirect_uri' => redirectURI,
									'code' => $code
									); 
// cURL is a library we use in PHP that calls on other API's
	// setting a cURL session and we put in $url because thats where we are getting data from
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, true);
	// setting the POSTFIELDS  to the arrat setup that we created
	curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings);
	// setting equal to bc we are getting strings back
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	// but in live work-production we want to set this to true
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      
     $result = curl_exec($curl);
     curl_close($curl);
     $results = json_decode($result, true);
	$userName = $results['user']['username'];
	$userID = getUserID($userName);
	printImages($userID);
     }
     else{
?>  

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Untitled</title>
	<link rel="author" href="humans.txt">
	<link rel="stylesheet" type="text/css" href="css/style.css"> 
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
</head>
<body>

	


	<a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code">
	LOGIN</a>
<script src="js/main.js"></script>
</body>
</html>
<?php
}
?>