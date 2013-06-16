<?php
/**
 * Project:     Unvstats
 * File:        player_sig.php
 *
 * For license and version information, see /index.php
 */

require_once 'core/init.inc.php';

if (!isset($_GET['player_id'])) {
  die('No player id given');
}

// basic info
$servername = SERVER_NAME;

switch( $_GET['style'] ) {
  case 1:
  case 2:
    $w = 500;
    $h = 40;
    break;
  default:
    $w = 400;
    $h = 46;
    break;
}

// Get the data
$stats = $db->GetRow("SELECT player_id,
                             player_name,
                             player_kills,
                             player_deaths
                      FROM players
                      WHERE player_id = ?",
                      array($_GET['player_id']));

if( !isset($stats['player_id']) ):
  die ("player id not found");
endif;

if( constant('PRIVACY_QUOTE') != '1' ):
  $random_quote = $db->GetRow("SELECT say_message
                               FROM says
                               WHERE say_player_id = ?
                               ORDER BY RAND()
                               LIMIT 0, 1",
                               array($_GET['player_id']));
endif;

$rgb = array(
  array(  51,  51,  51 ),
  array( 255,   0,   0 ),
  array(   0, 255,   0 ),
  array( 255, 255,   0 ),
  array(   0,   0, 255 ),
  array(   0, 255, 255 ),
  array( 255,   0, 255 ),
  array( 255, 255, 255 ),
  array( 255, 128,   0 ),
  array( 128, 128, 128 ),
  array( 191, 191, 191 ),
  array( 191, 191, 191 ),
  array(   0, 128,   0 ),
  array( 128, 128,   0 ),
  array(   0,   0, 128 ),
  array( 128,   0,   0 ),
  array( 128,  64,   0 ),
  array( 255, 153,  26 ),
  array(   0, 128, 128 ),
  array( 128,   0, 128 ),
  array(   0, 128, 255 ),
  array( 128,   0, 255 ),
  array(  51, 153, 204 ),
  array( 204, 255, 204 ),
  array(   0, 102,  51 ),
  array( 255,   0,  51 ),
  array( 179,  26,  26 ),
  array( 153,  51,   0 ),
  array( 204, 153,  51 ),
  array( 153, 153,  51 ),
  array( 255, 255, 191 ),
  array( 255, 255, 128 )
);


// safe to start image at this point
header("Content-type: image/png");


$im = imagecreatetruecolor($w, $h);

for($i = 0; $i < 32; $i++)
{
  $colors[$i]['alloc'] = imagecolorallocate($im, $rgb[$i][0], $rgb[$i][1], $rgb[$i][2]);
  $colors[$i]['alpha'] = imagecolorallocatealpha($im, $rgb[$i][0], $rgb[$i][1], $rgb[$i][2], 64);
}

function char_width( $size )
{
  switch( $size ) {
    case 1: return 5;
    case 2: return 6;
    case 3: return 7;
    case 4: return 8;
    default:
        break;
    }

  return 9;
}

function string_width( $size, $string ) {
  $w = 0;

  for ($i = 0, $l = strlen($string); $i < $l; $i++) {
    $c = $string[$i];

    if($c == '^' && $i < $l) {
      $c = $string[$i+1];
      if($c == '*' || ($c >= '0' && $c <= 'o')) {
        $i++;
        if ($c != '^')
          continue;
      }
    }
    $w += char_width( $size );
  }
  return $w;
}

function color_print( $x, $y, $string, $im, $colors, $size, $alpha ) {
  if($alpha == 1) {
    $color = $colors[7]['alpha'];
  } else {
    $color = $colors[7]['alloc'];
  }

  for ($i = 0, $l = strlen($string); $i < $l; $i++) {
    $c = $string[$i];

    if($c == '^' && $i < $l) {
      $c = $string[$i+1];

      if($c == '*') $c = '7';

      if($c == '^')
        $i++;
      else if($c >= '0' && $c <= 'o') {
        $i++;
        $c = (ord($c) - 48) & 31;
        if($alpha == 1) {
          $color = $colors[$c]['alpha'];
        } else {
          $color = $colors[$c]['alloc'];
        }
        continue;
      }
    }
    imagechar( $im, $size, $x, $y, $c, $color );
    $x += char_width( $size );
  }
}

switch( $_GET['style'] ) {
 case 1:
 case 2:
  if( $_GET['style'] == 1 ) {
    $b = imagecreatefrompng( "images/background1.png" );
    $ny = 4;
    $ty = 4;
  } else {
    $b = imagecreatefrompng( "images/background2.png" );
    $ny = 8;
    $ty = 6;
  }
  imagecopy( $im, $b, 0, 0, 0, 0, $w, $h );

  color_print( 10, $ny, $stats['player_name'], $im, $colors, 5, 0 );

  $sw = string_width( 5, $servername );
  color_print( 490 - $sw, 19, $servername, $im, $colors, 5, 1 );

  if( !empty( $random_quote['say_message'] ) ):
    $msg = "^2".substr( $random_quote['say_message'], 0, 59 );
    color_print( 14, 24, $msg, $im, $colors, 1, 1 );
  endif;

  $colorstat = imagecolorallocatealpha($im, 0xFF, 0xFF, 0xEE, 40);

  imagestring( $im, 5, 220, $ty, "Kills:", $colorstat );
  imagestring( $im, 5, 280, $ty, $stats['player_kills'], $colorstat );

  imagestring( $im, 5, 370, $ty, "Deaths:", $colorstat );
  imagestring( $im, 5, 440, $ty, $stats['player_deaths'], $colorstat );

  break;
default:
  $background = imagecolorallocate($im, 0x22, 0x26, 0x2a);
  imagefilledrectangle($im, 0, 0, $w - 1, $h - 1, $background);

  $border1 = imagecolorallocate($im, 0x57, 0x61, 0x6b);
  $border2 = imagecolorallocate($im, 0x04, 0x05, 0x05);

  imageline($im, 0, 0, $w - 1, 0, $border1);
  imageline($im, 0, 0, 0, $h - 1, $border1);
  imageline($im, 0, $h - 1, $w - 1, $h - 1, $border2);
  imageline($im, $w - 1, 0, $w - 1, $h - 1, $border2);

  color_print( 10, 5, $stats['player_name'], $im, $colors, 5, 0 );
  color_print( 10, 25, $servername, $im, $colors, 5, 1 );

  $colorstat = imagecolorallocatealpha($im, 0xFF, 0xFF, 0xFF, 40);

  imagestring( $im, 5, 250, 5, "Kills:", $colorstat );
  imagestring( $im, 5, 320, 5, $stats['player_kills'], $colorstat );

  imagestring( $im, 5, 250, 25, "Deaths:", $colorstat );
  imagestring( $im, 5, 320, 25, $stats['player_deaths'], $colorstat );

  break;
}


imagepng($im);
imagedestroy($im);

?>
