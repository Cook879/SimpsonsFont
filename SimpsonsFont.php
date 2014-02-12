<?php
/**
 * SimpsonsFont - Adds a <simpsons> tag to allow writing with the Simpsons font images on Wikisimpsons.
 *
 * @file
 * @ingroup Extensions
 * @version 1.0
 * @author Richard Cook <cook879@shoutwiki.com>
 * @copyright Copyright © 2014 Richard Cook
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 3.0 or later
 */

 // Check we're in MediaWiki
if( !defined( 'MEDIAWIKI' ) ) {
	die( "This is not a valid entry point.\n" );
}

// Extension credits that will show up on Special:Version
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'SimpsonsFont',
	'version' => '1.0',
	'author' => '[[User:Cook879|Richard Cook]]',
	'url' => 'http://simpsonswiki.com/wiki/Wikisimpsons:SimpsonsFont', 
	'description' => 'Adds a <nowiki><simpsons></nowiki> tag to allow writing with the Simpsons font images on [[Main Page|Wikisimpsons]].',
);

// Create SimpsonsFont tag
$wgHooks['ParserFirstCallInit'][] = 'wfSimpsonsSetup';

function wfSimpsonsSetup( Parser $parser ) {
		// Pair up the tag and the function
		$parser->setHook( 'simpsons', 'wfSimpsonsRender' );
		return true;
}

function wfSimpsonsRender( $text, array $args, Parser $parser, PPFrame $frame ) {
	// For security reasons, user input needs to be put through here
	$text = htmlspecialchars( $text );
	
	// Checks if user has used the size parameter
	if ( array_key_exists( 'size', $args ) ) {
		$size = $args['size'];
	}

	// Convert input into uppercase as font is uppercase only
	$text = strtoupper( $text );
	// Create an array of characters so file links can be generated
	$characterList = str_split( $text );
	// Create an output variable to add the image files to
	$output = '';
	foreach ( $characterList as $character ){
		// Check for a valid (i.e. one with an image) character
		if ( preg_match( '/([A-Z]|[0-9]|!|\?|\(|\))/', $character ) ) {
			// Check if the size variable was created
			if (isset ( $size ) ) {
				$output .= '[[File:SimpsonsF' . $character . '.png|' . $size . 'px|link=]]';
			} else {
				$output .= '[[File:SimpsonsF' . $character . '.png|link=]]';
			}
		} else if ( $character == ' ' ) {
			// Keep spaces intact
			$output .= ' ';
		} else {
			// Error message for invalid characters
			$output .= '<span style="color:red;">ERROR - Invalid character.</span>';
		}
	}
	// Parse the wikitext
	$output = $parser->recursiveTagParse( $output, $frame );
	// Return the generated output to the page
	return $output;
}
