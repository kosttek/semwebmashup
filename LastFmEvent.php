<?php


class LastFmEvent {
  var $artistId = null;
  var $apiKey = "71a45f829894597e4322585ed75d7f2b";
  function LastFmEvent($artistId){
    $this->artistId = $artistId;
  }

  function lastQuery(){
    $result = file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.getevents&mbid=".$this->artistId."&api_key=".$this->apiKey);

    $xml = new SimpleXMLElement($result);
    $i = 0;
    $events = array();
    foreach($xml->events->event as $event_xml){
      $event = array();
      $event["name"]=(string)$event_xml->title;
      $event["site"]=(string)$event_xml->url;
      $event["city"]=(string)$event_xml->venue->location->city;
      $event["country"]=(string)$event_xml->venue->location->country;
      $event["start_date"]=(string)$event_xml->startDate;
      
      $tags = array();
      foreach($event_xml->tags as $tag){
	array_push($tags,(string)$tag->tag);
      }
      $event["tags"]=$tags;
    
      $namespaces = $event_xml->getNameSpaces(true);
      $geo = $event_xml->venue->location->children($namespaces['geo']);
      $location = array();
      $location['lat']=(string)$geo->point->lat;
      $location['long']=(string)$geo->point->long;
      $event["location"]=$location;
      
      array_push($events,$event);
    }

    return $events;

    
    
  }

  
}

?>