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

if( ( $_SERVER[ 'HTTP_X_PURPOSE' ] ?? '' ) === 'preview' )
{
	$Link = 'https://image.thum.io/get/width/1200/crop/600/noanimate/https://' . CONFIG_HOSTNAME . '/' . $Code;
}

// Very long links break nginx, rely on html redirect instead
if( strlen( $Link ) < 8000 )
{
	header( 'Location: ' . $Link );
}

$Link = htmlspecialchars( $Link, ENT_HTML5 );

echo '<html>';
echo '<head><title>' . CONFIG_HOSTNAME . '</title>';
echo '<meta http-equiv="refresh" content="0;url=' . $Link . '"></head>';
echo '<body><a href="' . $Link . '">Continue…</a>';
echo '<script>location.href=document.querySelector("meta[http-equiv=refresh]").content.substr(6)</script></body>';
echo '</html>';
