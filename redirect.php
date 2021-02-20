<?php
declare(strict_types=1);

$RequestMethod = $_SERVER[ 'REQUEST_METHOD' ] ?? '';

if( $RequestMethod !== 'GET' && $RequestMethod !== 'HEAD' )
{
	http_response_code( 405 );
	exit;
}

require __DIR__ . '/common.php';

$Code = trim( $_SERVER[ 'DOCUMENT_URI' ] ?? '', '/' );

if( empty( $Code ) )
{
	http_response_code( 404 );

	echo '<html>';
	echo '<head><title>' . CONFIG_HOSTNAME . '</title></head>';
	echo '<body>';
	echo '<h1>This is a private url shortener.</h1>';
	echo '<h2>Nothing to see here.</h2>';
	echo '</body></html>';

	exit;
}

if( $Code === 'robots.txt' )
{
	header( 'Content-Type: text/plain' );

	echo "User-agent: *\nDisallow: /\n";

	exit;
}

$Id = $HashIds->decode( $Code );

if( count( $Id ) !== 2 || $Id[ 0 ] !== 1337 )
{
	http_response_code( 404 );
	exit;
}

$Link = $Database->prepare( 'SELECT `Link` FROM `links` WHERE `LinkId` = :id' );
$Link->bindParam( ':id', $Id[ 1 ], PDO::PARAM_INT );
$Link->execute();
$Link = $Link->fetch( PDO::FETCH_COLUMN );

if( empty( $Link ) )
{
	http_response_code( 404 );
	exit;
}

http_response_code( 301 );
header( 'Cache-Control: public, max-age=604800' );
header( 'Location: ' . $Link );

echo '<html>';
echo '<head><title>' . CONFIG_HOSTNAME . '</title></head>';
echo '<body><a href="' . htmlspecialchars( $Link, ENT_HTML5 ) . '">Continue…</a></body>';
echo '</html>';
