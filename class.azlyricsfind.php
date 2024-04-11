<?php

// Version 1.0  [11/04/2024]

class AZLyricsFind {

	function __construct($google_api_keys,$google_cx) {
        $this->google_api_keys = $google_api_keys;
        $this->google_cx = $google_cx;
    }

    public function get_lyrics_by_query($query) {
        if (empty($this->google_api_keys) || empty($this->google_cx)) return;
        $result = $this->get_first_google_result($query);
        if (!empty($result['error'])) return ['error'=>$result['error']];
        if ($result) $lyrics = $this->scrape_az_lyrics($result['url']);
        if (empty($lyrics)) return ['error'=>'Scraping failed.'];
        $result['lyrics'] = $lyrics;
        return $result;
    }

    //////////////////////////////////////////////////////////////////
    
    private $google_api_keys, $google_cx;

    private function get_first_google_result($query) {
        $queryEncoded = urlencode($query);
        $api_key_index = array_rand($this->google_api_keys); // pick one at random 
        $api_key = $this->google_api_keys[$api_key_index];
        $url = "https://www.googleapis.com/customsearch/v1?q=$queryEncoded&key=$api_key&cx={$this->google_cx}";
        $responseData = json_decode($this->fetch($url),true);
        if (isset($responseData["error"]["message"])) {
        	return ['error'=>"Google: {$responseData["error"]["message"]}"];
        }
        if (isset($responseData["searchInformation"]["totalResults"]) && $responseData["searchInformation"]["totalResults"]===0) {
        	return ['error'=>"Google: 0 results"];
        }
        if (empty($responseData['items'][0])) {
        	return ['error'=>"Google: Search failed (Unknown error)"];
        }
        $title_parts = explode(" Lyrics",$responseData['items'][0]['title']);
        $title = $title_parts[0];
        return array(
            'title' => $title,
            'confidence' => $this->confidence($query,$title),
            'url' => $responseData['items'][0]['link'],
        	'gapi_key' => $api_key,
        );
    }

    private function confidence($query,$title) {
    	// this function can be improved. i know
	    $query_tokens = array_unique(preg_split('/\s+/', strtolower($query), -1, PREG_SPLIT_NO_EMPTY));
	    $title_tokens = array_unique(preg_split('/\s+/', strtolower($title), -1, PREG_SPLIT_NO_EMPTY));
	    $intersection_count = count(array_intersect($query_tokens, $title_tokens));
	    $max_token_count = max(count($query_tokens), count($title_tokens));
	    $similarity = $max_token_count > 0 ? $intersection_count / $max_token_count : 0;
	    $confidence = round($similarity * 100);
	    return $confidence;
    }

    private function scrape_az_lyrics($url) {
        $html_content = $this->fetch($url);
        if (!$html_content) return;
        $start_at = "third-party lyrics provider is prohibited by our licensing agreement. Sorry about that. -->";
        $end_at = "<!-- MxM banner -->";
        $html_parts = explode($start_at,$html_content,2);
        if (!isset($html_parts[1])) return;
        $html_parts = explode($end_at,$html_parts[1],2);
        if (!isset($html_parts[1])) return;
        $lyrics = str_replace("<br>","",$html_parts[0]);
        $lyrics = str_replace("</div>","",$lyrics);
        $lyrics = html_entity_decode(trim($lyrics));
        if ($lyrics) return $lyrics;
    }

    private function fetch($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $html_content = curl_exec($curl);
        if (curl_errno($curl)) return;
        curl_close($curl);
        return $html_content;
    }

}

?>
