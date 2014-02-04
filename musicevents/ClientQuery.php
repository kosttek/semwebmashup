<?php
 
// ----------------------------------------------------------------------------------
// Class: ClientQuery
// ----------------------------------------------------------------------------------
 
/**
 * ClientQuery Object to run a SPARQL Query against a SPARQL server.
 *
 * @version  $Id: fsource_sparql__sparqlClientQuery.php.html 443 2007-06-01 16:25:38Z cax $
 * @author   Tobias Gauß <tobias.gauss@web.de>
 *
 * @package sparql
 */
 
class ClientQuery  {
 
  var $default  = array();
  var $named    = array();
  var $prefixes = array();
  var $query;
 
  /**
   * Adds a default graph to the query object.
   *
   * @param String  $default  default graph name
   */
  function addDefaultGraph($default){
    if(!in_array($this->named,$this->default))
      $this->default[] = $default;
  }
  /**
   * Adds a named graph to the query object.
   *
   * @param String  $default  named graph name
   */
  function addNamedGraph($named){
    if(!in_array($named,$this->named))
      $this->named[] = $named;
  }
  /**
   * Adds the SPARQL query string to the query object.
   *
   * @param String  $query the query string
   */
  function query($query){
    $this->query = $query;
  }
  function toString() {
    $objectvars = get_object_vars($this);
    foreach($objectvars as $key => $value) 
      $content .= $key ."='". $value. "'; ";
    return "Instance of " . get_class($this) ."; Properties: ". $content;
  }
}
 
 
 
 
?>