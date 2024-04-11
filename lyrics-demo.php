<?php

// Google Search API Keys (free limit is 100 requests/day. Minimum 1 key)
// Get API key from https://developers.google.com/custom-search/v1/introduction 
// (Click 'Get A Key')
$google_api_keys = [
    '__YOUR_GOOGLE_SEARCH_API_KEY_1__',
    '__YOUR_GOOGLE_SEARCH_API_KEY_2__',
];

// Google Custom Search Engine CX
// Create from here: https://programmablesearchengine.google.com/controlpanel/all
// Click 'Add' and include only URLs that start with www.azlyrics.com/lyrics/*
$google_cx = '__YOUR_GOOGLE_SEARCH_ENGINE_CX__';

// Initialize finder
require_once(__DIR__.'/class.azlyricsfind.php');
$finder = new AZLyricsFind($google_api_keys,$google_cx);

// Get lyrics
$query = "quicksilver - don't cry my lady love";
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
