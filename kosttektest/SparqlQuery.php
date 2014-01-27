<?php

//class parse usage data
// artistID
// name
// description
// homepage
/*
  args 
   1. artist name for example : Bonobo
   2. artist index ex. 2 (there is 2 bonobos in music brain so we must chose one of them number 2 is Simon Green UK elctronic musican)

  return array map with data

 */
class SparqlQuery {
  var  $client;

  var $artistId = null;
  var $name = null;
  var $homepage = null;
  var $desc = null;
  var $artist_amount = null;

  function SparqlQuery(){
    $this->client =  new SparqlClient("http://dbtune.org/musicbrainz/sparql");
  }
  function queryArtist($name,$number){
    $this->artistId =  $this->queryUri($name,$number);
    
    $qureystring3 = "
SELECT ?property ?hasValue ?isValueOf
WHERE {
  { <".$this->artistId."> ?property ?hasValue }
  UNION
  { ?isValueOf ?property <".$this->artistId."> }
}
ORDER BY (!BOUND(?hasValue)) ?property ?hasValue ?isValueOf
";
    $result = $this->qu($qureystring3);
    $this->filter_result($result);
    /* echo "name: ".$this->name."\n"; */
    /* echo "homepage: ".$this->homepage."\n"; */
    /* echo "description: ".$this->desc."\n"; */
    $arr = array();
    $arr["name"] = $this->name;
    $arr["homepage"] = $this->homepage;
    $arr["description"] = $this->desc; 
    $arr["artistamount"] = $this->artist_amount; 
    $arr["artistid"] = $this->getArtistId(); 
    return $arr;
  }

  function getArtistId(){
    $array = explode("/", $this->artistId);
    end($array);         // move the internal pointer to the end of the array
    $id = current($array);
    return $id;
  }

  function queryUri($name,$number){
    $querystring2 ="PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX mo: <http://purl.org/ontology/mo/>
SELECT *
WHERE {
   ?artist a mo:MusicArtist;
      foaf:name \"".$name."\".
}
";

    $result = $this->qu($querystring2);
    $sparql = new SimpleXMLElement($result);
    $results_size = count($sparql->results->result);
    $this->artist_amount = $results_size;
    if ($results_size < $number){
      $number = $results_size;
    }
    $number = $number - 1;
    $artist_uri = $sparql->results->result[$number]->binding->uri;
    return $artist_uri;
  }

  function qu($qu_string){
    $query = new ClientQuery();
    $query->query($qu_string);
    $this->client->setOutputFormat("xml");
    //string
    $result = $this->client->query($query);
    return $result;
  }

  function filter_result($result){
        $sparql = new SimpleXMLElement($result);
	$results_size = count($sparql->results->result);
	for( $i =0 ; $i<$results_size;$i++){
	  //	  echo $sparql->results->result[$i]->binding[0]->uri."\n";
	  if($sparql->results->result[$i]->binding[0]->uri == 'http://xmlns.com/foaf/0.1/name'){
	    $this->name = $sparql->results->result[$i]->binding[1]->literal;    
	  }elseif($sparql->results->result[$i]->binding[0]->uri == 'http://xmlns.com/foaf/0.1/homepage'){
	    $this->homepage = $sparql->results->result[$i]->binding[1]->uri;
	  }elseif($sparql->results->result[$i]->binding[0]->uri == 'http://purl.org/dc/elements/1.1/description'){
	    $this->desc = $sparql->results->result[$i]->binding[1]->literal;
	  }
	}
  }

}


?>