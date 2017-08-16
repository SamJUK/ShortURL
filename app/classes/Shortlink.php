<?php

class Shortlink 
{

	protected $htaccess = "../.htaccess";
	protected $charString = "abcdefghkmoprstuvxyz"; // As a string - exploded to array later ( Better readabilty )
	protected $charArray = [];
	protected $shortLength = 5;

	protected $JSON = '';

	/**
	 * Constructor function for our Shortlink class. 
	 *
	 * @param String $url URL to shorten
	 */
	function __construct ($url)
	{
		// Explode our string of characters into an array
		$this->explodeChars($this->charString);

		// Let's generate a random string to use as our URL
		$shortURL = $this->generateShortURL($this->charArray, $this->shortLength);
	
		// Check if that short URL is already in use
		$doesURLExist = $this->doesURLExist($shortURL);

		// Normalise long url 
		$longURL = $this->normaliseLongURL($url);

		// URL Exists so throw error
		if ($doesURLExist)
		{	$this->JSON = '{ "Success": false, "Message": "Application Error: That Short URL is already in use!"}';
			//Header('Location: error.php?e=1');
		}else{
			// Create URL
			$this->addRedirect($shortURL, $longURL);
			// Return success state
			$this->JSON = '{ "Success": true, "ShortLink": "'.$shortURL.'", "LongURL": "'.$url.'"}';
		};

		// Echo short url to page
		// echo "Your new short URL is: http://sam.dev/shortLink2/{$shortURL}";
	}

	/**
	 * Explode a string of characters into our character array.
	 * To generate a random URL with.
	 *
	 * @param String $charString String of characters to explode
	 */
	private function explodeChars ($charString)
	{
		$this->charArray = str_split($charString);
	}

	/**
	 * Generates a random URL from character array with set length.
	 *
	 * @param Array $chars Array of characters to use
	 * @param Int $length Length that the url should be
	 *
	 * @return String The randomly generated URL 
	 */
	private function generateShortURL ($chars, $length)
	{
		$url = "";

		// Get a random index from the chars array and append them to the URL variable
		$randomIndexs = array_rand($chars, $length);
		foreach ($randomIndexs as $index)
		{
			$url .= $chars[$index];	
		};

		return $url;
	}

	/**
	 * Checks whether a given URL already exists
	 *
	 * @param String $url The url that you want to check
	 *
	 * @return Bool Whether it exists or not
	 */
	private function doesURLExist ($url)
	{

		$redirects = [];

		$lines = file($this->htaccess);
		foreach ($lines as $line_index => $line) {
			$exploded = explode(' ', $line);
			// For each rewrite rule
			if ($exploded[0] == "RewriteRule")
			{
				// Checks sub URL against specified URL
				 if (substr($exploded[1],1, 5) == $url)
				 	return true;
			}
		};

		return false;
	}

	/**
	 * Adds the redirect to the HTACCESS file
	 *
	 * @param String $shortURL URL to shorten to
	 * @param String $fullURL URL to redirect to µ
	 */
	private function addRedirect ($shortURL, $fullURL)
	{

		// Exit if htaccess does not exist
		if ( !file_exists($this->htaccess) )
			return;

		// New .htaccess string
		$newContent = "";

		// New Redirect
		$newRedirect = "RewriteRule ^{$shortURL}(.*)$ {$fullURL} [L,R=301]\n";

		// Open file and dump content into an array
		$lines = file ($this->htaccess);
		foreach ($lines as $line_num => $line) {
			// If its the last line before the closing tag, add the new redirect first
			if ($line_num == count($lines) - 1)
				$newContent .= $newRedirect;
			
			$newContent .= $line;
		};

		file_put_contents($this->htaccess, $newContent);
	}

	/**
	 * Return the JSON stored in variable E.G Success State
	 * 
	 * @return String JSON
	 */
	public function retreiveJSONState()
	{
		return $this->JSON;
	}

	/**
	 * Static function to check whether the URL has already been shortend
	 *
	 * @param String $URL Url to check
	 * @return Boolean
	 */
	public static function hasURLbeenShortend($url)
	{
		// Open HTACCESS and read each line
		$redirects = [];
		$lines = file('../.htaccess');
		foreach ($lines as $line_index => $line) {
			$exploded = explode(' ', $line);
			// For each rewrite rule
			if ($exploded[0] == "RewriteRule")
			{
				if( $exploded[2] == $url )
					return true;
			};
		};
		return false;
	}

	/**
	 * Normalise URL string's; E.G make sure all url's have a HTTP Prefix etc
	 *
	 * @param String $url Url to normalise
	 * @return String Normalised URL
	 */
	private function normaliseLongURL($url)
	{
		// Make sure there is an HTTP/S prefix
		if ( strtolower(substr($url, 0, 3)) != 'http')
			return 'http://' . $url;

		return $url;
	}
}