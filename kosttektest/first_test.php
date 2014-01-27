<?php
//define("RDFAPI_INCLUDE_DIR", "/home/kosttek/Studia/9semestr/wshop/rapSPARQLlib/rdfapi-php/api/"); 
//include(RDFAPI_INCLUDE_DIR . "RdfAPI.php"); 

include("SparqlClient.php");
include("ClientQuery.php");
include("SparqlQuery.php");
include("LastFmEvent.php");

$spq = new SparqlQuery();
$arr = $spq->queryArtist("Bonobo",1);

print_r($arr);

$lfe  = new  LastFmEvent($arr["artistid"]);
$events = $lfe->lastQuery();

print_r($events);



?>