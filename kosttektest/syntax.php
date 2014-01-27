<?php
/**
 * Plugin Skeleton: Displays "Hello World!"
 *
 * Syntax: <TEST> - will be replaced with "Hello World!"
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Christopher Smith <chris@jalakai.co.uk>
 */

include("SparqlClient.php");
include("ClientQuery.php");
include("SparqlQuery.php");
include("LastFmEvent.php");
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_kosttektest extends DokuWiki_Syntax_Plugin {
 
 
 
   /**
    * Get the type of syntax this plugin defines.
    *
    * @param none
    * @return String <tt>'substition'</tt> (i.e. 'substitution').
    * @public
    * @static
    */
    function getType(){
        return 'substition';
    }
 
    /**
     * What kind of syntax do we allow (optional)
     */
//    function getAllowedTypes() {
//        return array();
//    }
 
   /**
    * Define how this plugin is handled regarding paragraphs.
    *
    * <p>
    * This method is important for correct XHTML nesting. It returns
    * one of the following values:
    * </p>
    * <dl>
    * <dt>normal</dt><dd>The plugin can be used inside paragraphs.</dd>
    * <dt>block</dt><dd>Open paragraphs need to be closed before
    * plugin output.</dd>
    * <dt>stack</dt><dd>Special case: Plugin wraps other paragraphs.</dd>
    * </dl>
    * @param none
    * @return String <tt>'block'</tt>.
    * @public
    * @static
    */
//    function getPType(){
//        return 'normal';
//    }
 
   /**
    * Where to sort in?
    *
    * @param none
    * @return Integer <tt>6</tt>.
    * @public
    * @static
    */
    function getSort(){
        return 999;
    }
 
 
   /**
    * Connect lookup pattern to lexer.
    *
    * @param $aMode String The desired rendermode.
    * @return none
    * @public
    * @see render()
    */
    function connectTo($mode) {
      //      $this->Lexer->addSpecialPattern('<KOSTTEK>',$mode,'plugin_kosttektest');
      $this->Lexer->addEntryPattern('<musicevent>',$mode,'plugin_kosttektest');
    }


 
   function postConnect() {
     $this->Lexer->addExitPattern('</musicevent>','plugin_kosttektest');
   }
 
 
   /**
    * Handler to prepare matched data for the rendering process.
    *
    * <p>
    * The <tt>$aState</tt> parameter gives the type of pattern
    * which triggered the call to this method:
    * </p>
    * <dl>
    * <dt>DOKU_LEXER_ENTER</dt>
    * <dd>a pattern set by <tt>addEntryPattern()</tt></dd>
    * <dt>DOKU_LEXER_MATCHED</dt>
    * <dd>a pattern set by <tt>addPattern()</tt></dd>
    * <dt>DOKU_LEXER_EXIT</dt>
    * <dd> a pattern set by <tt>addExitPattern()</tt></dd>
    * <dt>DOKU_LEXER_SPECIAL</dt>
    * <dd>a pattern set by <tt>addSpecialPattern()</tt></dd>
    * <dt>DOKU_LEXER_UNMATCHED</dt>
    * <dd>ordinary text encountered within the plugin's syntax mode
    * which doesn't match any pattern.</dd>
    * </dl>
    * @param $aMatch String The text matched by the patterns.
    * @param $aState Integer The lexer state for the match.
    * @param $aPos Integer The character position of the matched text.
    * @param $aHandler Object Reference to the Doku_Handler object.
    * @return Integer The current lexer state for the match.
    * @public
    * @see render()
    * @static
    */
    function handle($match, $state, $pos, &$handler){
        switch ($state) {
          case DOKU_LEXER_ENTER : 
            break;
          case DOKU_LEXER_MATCHED :
            break;
          case DOKU_LEXER_UNMATCHED :
	    return array($state,$this->query($match));
            break;
          case DOKU_LEXER_EXIT :
            break;
          case DOKU_LEXER_SPECIAL :
            break;
        }
        return array($state,'');
    }
 

    function query($match){
      
      $pieces = explode(',',$match);
      $artist_name=$pieces[0];
      $number = intval($pieces[1]);
      $spq = new SparqlQuery();
      $arr = $spq->queryArtist($artist_name,$number);

      if($arr["artistamount"]==0){
	return "There is no artist named ".$artist_name.". Try to use The before name.";
      }

      $lfe  = new  LastFmEvent($arr["artistid"]);
      $events = $lfe->lastQuery();

      $result = array();
      $result['info']=$arr;
      $result['events']=$events;

      $res_string = (string)$arr['name'];
      $res_string = "<b>".$res_string."</b></br>";
      $res_string = $res_string."<table>";
      foreach ($events as $event){
	$temp ="<tr>";
	$temp=$temp."<td><a href =".(string)$event['site'].">".(string)$event['name']."</a></td>";
	$temp=$temp."<td>".(string)$event['city']."</td>";
	$temp=$temp."<td>".(string)$event['start_date']."</td>";
	$tags_str = "";
	foreach ($event["tags"] as $tag){
	  $tags_str=$tags_str.(string)$tag."  ";
	}
	$temp=$temp."<td>".$tags_str."</td>";
	$res_string = $res_string.$temp."</tr>";
      }
      $res_string = $res_string."</table>";
      return $res_string;
      

    }


   /**
    * Handle the actual output creation.
    *
    * <p>
    * The method checks for the given <tt>$aFormat</tt> and returns
    * <tt>FALSE</tt> when a format isn't supported. <tt>$aRenderer</tt>
    * contains a reference to the renderer object which is currently
    * handling the rendering. The contents of <tt>$aData</tt> is the
    * return value of the <tt>handle()</tt> method.
    * </p>
    * @param $aFormat String The output format to generate.
    * @param $aRenderer Object A reference to the renderer object.
    * @param $aData Array The data created by the <tt>handle()</tt>
    * method.
    * @return Boolean <tt>TRUE</tt> if rendered successfully, or
    * <tt>FALSE</tt> otherwise.
    * @public
    * @see handle()
    */

    
    function render($mode, &$renderer, $data) {


        if($mode == 'xhtml'){
	  list($state, $match) = $data; // important
	  switch ($state) {
            case DOKU_LEXER_UNMATCHED : 
	      $renderer->doc .= $match;            // ptype = 'normal'
	      break;
	  
	  }

            
//            $renderer->doc .= "<p>Hello World!</p>";     // ptype = 'block'
            return true;
        }
        return false;
    }

    /* function resultToHtmlTable($match){ */
    /*   //      $result = '<tr>'.'<td> lol </td>'.'</tr>'; */
    /*   $result = 'lol'; */
    /*   return $result; */
    /* } */
}
 

//Setup VIM: ex: et ts=4 enc=utf-8 :
?>






