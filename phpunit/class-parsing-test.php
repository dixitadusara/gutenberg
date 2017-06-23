<?php
/**
 * PEG.js parser (PHP code target) tests
 *
 * @package Gutenberg
 */

class Parsing_Test extends WP_UnitTestCase {
	protected static $fixtures_dir;

	function parsing_test_filenames() {
		if ( ! getenv( 'RUN_PARSER_TESTS' ) ) {
			// Use the RUN_PARSER_TESTS environment variable to run these tests.
			return array();
		}
		self::$fixtures_dir = dirname( dirname( __FILE__ ) ) . '/blocks/test/fixtures';

		require_once dirname( dirname( __FILE__ ) ) . '/lib/parser.php';

		$fixture_filenames = glob( self::$fixtures_dir . '/*.{json,html}', GLOB_BRACE );
		$fixture_filenames = array_values( array_unique( array_map(
			array( $this, 'clean_fixture_filename' ),
			$fixture_filenames
		) ) );

		return array_map(
			array( $this, 'pass_parser_fixture_filenames' ),
			$fixture_filenames
		);
	}

	function clean_fixture_filename( $filename ) {
		$filename = basename( $filename );
		$filename = preg_replace( '/\..+$/', '', $filename );
		return $filename;
	}

	function pass_parser_fixture_filenames( $filename ) {
		return array(
			"$filename.html",
			"$filename.parsed.json",
		);
	}

	/**
	 * @dataProvider parsing_test_filenames
	 */
	function test_parser_output( $html_filename, $parsed_json_filename ) {
		$html_filename        = self::$fixtures_dir . '/' . $html_filename;
		$parsed_json_filename = self::$fixtures_dir . '/' . $parsed_json_filename;

		foreach ( array( $html_filename, $parsed_json_filename ) as $filename ) {
			if ( ! file_exists( $filename ) ) {
				throw new Exception( "Missing fixture file: '$filename'" );
			}
		}

		$html   = file_get_contents( $html_filename );
		$parsed = json_decode( file_get_contents( $parsed_json_filename ), true );

		$parser = new PhpPegJs\Parser;
		$result = $parser->parse( $html );

		error_log( json_encode( compact( 'html', 'result' ) ) );

		$this->assertEquals( $parsed, $result );
	}
}
