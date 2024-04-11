<?php

$query = "quicksilver - don't cry my lady love";

// Google Search API Key (free limit is 100 requests/day. Minimum 1 key)
$google_api_keys = [
    'AIzaSyCQ6LZGhv9fcd-v3Z2qlV1VP273GLtw', // example
    'AIzaSyDDppvfDbQH9MyNUWNBf_CKTd240_g1Y', // example
];

// Google Custom Search Engine CX
$google_cx = 'd67a8a54c4f40cf'; // example

// Initialize finder
require_once(__DIR__.'/class.azlyricsfind.php');
$finder = new AZLyricsFind($google_api_keys,$google_cx);

// Get lyrics
$result = $finder->get_lyrics_by_query($query);

// Check for error
if (isset($result['error'])) {
	echo "Error: {$result['error']}";

// Lyrics found (may be wrong song, check confidence)
} else {
	echo "<h1>{$result['title']}</h1>";					// title
	echo "<pre>{$result['lyrics']}</pre>";				// lyrics
	echo "<p>Confidence: {$result['confidence']}<p>";	// confidence level (0-100)
	echo "<p>URL: {$result['url']}</p>";				// azlyrics url
	echo "<p>Google Api key: {$result['gapi_key']}<p>";	// google api key used
}

?>
