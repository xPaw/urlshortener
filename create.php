<?php
declare(strict_types=1);

header( 'Content-Type: text/plain' );

if( ( $_SERVER[ 'REQUEST_METHOD' ] ?? '' ) !== 'POST' )
{
	http_response_code( 405 );
	exit( 'Send a POST request with `secret` and `url` fields.' );
}

require __DIR__ . '/common.php';

$Url = (string)filter_input( INPUT_POST, 'url', FILTER_SANITIZE_URL );
$Secret = (string)filter_input( INPUT_POST, 'secret', FILTER_SANITIZE_STRING );

if( !hash_equals( CONFIG_CREATE_SECRET, $Secret ) )
{
	http_response_code( 400 );
	exit( 'Invalid secret.' );
}

if( str_contains( $Url, "\n" ) || str_contains( $Url, "\r" ) )
{
	http_response_code( 400 );
	exit( 'Passed url contains line breaks.' );
}

$ParsedUrl = parse_url( $Url );

if( $ParsedUrl === false )
{
	http_response_code( 400 );
	exit( 'Passed url is malformed.' );
}

if( empty( $ParsedUrl[ 'host' ] ) )
{
	http_response_code( 400 );
	exit( 'Url has no host.' );
}

if( ( $ParsedUrl[ 'scheme' ] ?? '' ) !== 'https' )
{
	http_response_code( 400 );
	exit( 'Only https:// urls are supported.' );
}

$Database->prepare( 'INSERT INTO `links` (`Link`) VALUES (?)' )->execute( [ $Url ] );
$Id = $Database->lastInsertId();

$Code = $HashIds->encode( [ 1337, $Id ] );

$ShortLink = 'https://' . CONFIG_HOSTNAME . '/' . $Code;

http_response_code( 201 );
header( 'Location: ' . $ShortLink );

echo $ShortLink;
