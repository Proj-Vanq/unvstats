<?php
/**
 * Project:     Unvstats
 * File:        _graph.php
 *
 * For license and version information, see /index.php
 */

if (CLIENT_IS_BOT)
{
    function graph_killsPerGame($player_id) {}
    function graph_killsInGame($game_id) {}
    function graph_winsOnMap($map_id) {}
    function graph_mapBalanceBar($alien, $tie, $human) {}
}
else // not a bot
{
    function graphlib_calcSpoke($r, $angle, $limit)
    {
        $angle = deg2rad($angle * 360.0 / $limit);
        return array(-sin($angle) * $r, -cos($angle) * $r);
    }

    function graphlib_makeSector($x, $y, $r, $start, $end, $limit, $class)
    {
        if ($end == 0)
            return array();

        static $clipID = 0;
        ++$clipID;

        if ($start == 0 && $end == $limit)
            return array('drawing' => "<circle class='$class' cx='$x' cy='$y' r='$r' />\n");

        $end += $start;
        $rx = $r * 1.2;

        $point = graphlib_calcSpoke($rx, $start, $limit);
        $result = "<clipPath id='clipPath_$clipID'><polygon points='$x,$y "
                . (string)round($x + $point[0]) . ',' . (string)round($y + $point[1]);

        $p_end = floor($end * 16.0 / $limit);
        for ($p = ceil($start * 16.0 / $limit) + 1; $p <= $p_end; ++$p) {
            $point = graphlib_calcSpoke($rx, $p, 16);
            $result .= ' ' . (string)round($x + $point[0]) . ',' . (string)round($y + $point[1]);
        }

        $point = graphlib_calcSpoke($rx, $end, $limit);
        $result .= ' ' . (string)($x + $point[0]) . ',' . (string)round($y + $point[1]);

        return array('clip' => $result . "' /></clipPath>\n", 'drawing' => "<circle class='$class' cx='$x' cy='$y' r='$r' clip-path='url(#clipPath_$clipID)' />");
    }

    function graphlib_calcAxisMarkings($length, $ppu)
    {
        $mark = $length;
        $skip = floor(log($mark) / log(10)) + 1;
        $mark /= pow(10, $skip); // >= 0.1, < 1
        $mark = ceil($mark * 20);
        if ($mark >= 14) $mark = 20; else if ($mark >= 7) $mark = 10; else if ($mark >= 3) $mark = 4;
        $mark *= pow(10, $skip - 1) / 40.0;

        $number = 1;
        while ($mark * $number * $ppu < 80)
            $number *= 2;

        return array($mark, $number);
    }

    function graphlib_drawData($xo, $yo, $unit_x, $unit_y, $data_y, $class, $clip)
    {
        $num = count($data_y) + 1;
        $data_y[] = 0;
        $data_y[] = 0;
        $data_y[] = 0;

        echo "<path clip-path='url(#$clip)' class='line $class' d='M $xo,$yo S";
        $y  = $yo;
        $ny = $yo;
        for ($a = 0; $a <= $num; ++$a)
        {
            $x = $xo + $a * $unit_x;
            $py = $y;
            $y = $ny;
            $ny = $yo - $data_y[$a] * $unit_y;

            echo ' ', $x - $unit_x / 3, ',', $y - ($ny - $py) / 6, ' ', $x, ',', $y;

        }
        echo "' />\n";
    }

    function graphlib_drawGraph($min_x, $max_x, $min_y, $max_y, $data)
    {
        if ($max_x - $min_x == 0 || $max_y - $min_y == 0)
            return;

        static $clip = 0;
        ++$clip;

        $xo = 31.5;
        $yo = 180.5;
        $xe = 671.5;
        $ye = 10.5;
        $xl = $xe - $xo;
        $yl = $yo - $ye;

        $unit_x = $xl / ($max_x - $min_x);
        $unit_y = $yl / ($max_y - $min_y);

        echo <<<EOF
<svg width='800' height='200' version='1.1' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg'>
<defs>
  <clipPath id='graph_clip_$clip'><rect fill='black' stroke-width='0' x='$xo' y='$ye' width='$xl' height='$yl' /></clipPath>
</defs>
<path d='M $xo,$ye L $xo,$yo L $xe,$yo' class='line' />
EOF;

        // X axis
        list($mark, $number) = graphlib_calcAxisMarkings($max_x - $min_x, $unit_x);
        $i = true;
        for ($a = $min_x - fmod($min_x, $mark); $a <= $max_x; $a += $mark)
        {
            $b = $xo + ($a - $min_x) * $unit_x;
            $r = fmod($a / $mark, $number) == 0;
            if ($b < $xo) { $v = $min_x; $b = $xo; } else $v = $a;
            if ($r || $i) echo "<text x='$b' y='", $yo + 5, "' class='axis-x'>$v</text>\n";
            echo "<line x1='$b' y1='$yo' x2='$b' y2='", $yo - ($r ? 6 : 3), "' />\n";
            $i = false;
        }

        // Y axis
        list($mark, $step) = graphlib_calcAxisMarkings($max_y - $min_y, $unit_y);
        $i = true;
        for ($a = $min_y - fmod($min_y, $mark); $a <= $max_y; $a += $mark)
        {
            $b = $yo - ($a - $min_y) * $unit_y;
            $r = fmod($a / $mark, $number) == 0;
            $v = $a;
            if ($b > $yo) { $v = $min_y; $b = $yo; } else $v = $a;
            if ($r || $i)
            {
                echo "<text x='", $xo - 5, "' y='$b' class='axis-y'>$v</text>\n";
                if ($b != $yo)
                    echo "<line x1='$xo' y1='$b' x2='$xe' y2='$b' class='grid' />\n";
            }
            echo "<line x1='$xo' y1='$b' x2='", $xo + ($r ? 6 : 3), "' y2='$b' />\n";
            $i = false;
        }

        $i = 0;
        $xl = $xe + 16;
        $xt = $xl + 21;
        foreach ($data as $line)
        {
            graphlib_drawData($xo, $yo, $unit_x, $unit_y, $line['values'], $line['class'], "graph_clip_$clip");

            $iyl = 24 * ++$i;
            $iyt = $iyl + 7;
            echo <<<EOF
<rect class='$line[class]' x='$xl' y='$iyl' width='16' height='4' />
<text x='$xt' y='$iyt'>$line[key]</text>
EOF;
        }

        echo "</svg>\n";
    }



    function graph_killsPerGame($player_id)
    {
        global $db;

        $limit = 100;
        $stats = $db->GetAll('SELECT stats_kills,
                                     stats_teamkills,
                                     stats_deaths
                              FROM per_game_stats
                              WHERE stats_player_id = ?
                              LIMIT ?',
                              array($_GET['player_id'], $limit));
        $count = $db->GetAll('SELECT COUNT(stats_player_id) AS count
                              FROM per_game_stats
                              WHERE stats_player_id = ?',
                              array($_GET['player_id']));
        $count = $count[0]['count'];

        $i = 1;
        $kill_data  = array();
        $teamkill_data  = array();
        $death_data  = array();
        foreach ($stats AS $stat) {
          $kill_data[]  = $stat['stats_kills'];
          $teamkill_data[]   = $stat['stats_teamkills'];
          $death_data[]  = $stat['stats_deaths'];

          $i++;
        }

        // Check data
        if ($i == 1) {
          $kill_data  = array(0, 0);
          $teamkill_data  = array(0, 0);
          $death_data  = array(0, 0);
          $i = 3;
        } elseif ($i == 2) {
          $kill_data[]  = $kill_data[0];
          $teamkill_data[]  = $teamkill_data[0];
          $death_data[]  = $death_data[0];
          $i = 3;
        }

        graphlib_drawGraph(max(0, $count - $limit), $count, 0, max(10, max($kill_data), max($teamkill_data), max($death_data)),
                           array(array('values' => $kill_data,     'class' => 'player-kills',     'key' => 'Kills'),
                                 array('values' => $teamkill_data, 'class' => 'player-teamkills', 'key' => 'Team kills'),
                                 array('values' => $death_data,    'class' => 'player-deaths',    'key' => 'Deaths')));
    }

    function graph_killsInGame($game_id)
    {
        global $db;

        $stats = $db->GetAll('SELECT kill_type,
                                     kill_weapon_id,
                                     kill_gametime,
                                     kill_id
                              FROM kills
                              WHERE kill_game_id = ?
                              ORDER BY kill_id',
                              array($game_id));
        $weapons = $db->GetAll('SELECT weapon_id,
                                       weapon_team
                                FROM weapons');
        $game = $db->GetRow('SELECT game_length
                             FROM games
                             WHERE game_id = ?',
                             array($game_id));

        sscanf($game['game_length'], '%d:%d:%d', $hh, $mm, $ss);
        $maxoffset = (($hh * 60 + $mm) * 60 + $ss) / 60;
        $yscale = 0;

        $length = count($stats);
        $offset = 1;

        $alien_data = array();
        $human_data = array();
        $world_data = array();
        foreach ($stats AS $stat) {
          sscanf($stat['kill_gametime'], '%d:%d:%d', $hh, $mm, $ss);
          $stamp = (($hh * 60 + $mm) * 60 + $ss) / 60;
          while ($offset < 2 || $offset < $stamp) {
            $alien_data[] = 0;
            $human_data[] = 0;
            $world_data[] = 0;
            $offset++;
          }
          $id = $stat['kill_weapon_id'];
          $team = $weapons[$id]['weapon_team'];
          if ($team == 'alien' ) {
            end($alien_data);
            $k = key($alien_data);
            $alien_data[$k]++;
            if ($alien_data[$k] > $yscale) $yscale = $alien_data[$k];
          }
          else if($team == 'human' ) {
            end($human_data);
            $k = key($human_data);
            $human_data[$k]++;
            if ($human_data[$k] > $yscale) $yscale = $human_data[$k];
          }
          else {
            end($world_data);
            $k = key($world_data);
            $world_data[$k]++;
            if ($world_data[$k] > $yscale) $yscale = $world_data[$k];
          }
        }
        while ($offset < 3 || $offset <= $maxoffset ) {
          $alien_data[] = 0;
          $human_data[] = 0;
          $world_data[] = 0;
          $offset++;
        }

        if ($yscale < 10 ) $yscale = 10;

        graphlib_drawGraph(0, $maxoffset, 0, $yscale,
                           array(array('values' => $alien_data, 'class' => 'alien', 'key' => 'Alien kills'),
                                 array('values' => $human_data, 'class' => 'human', 'key' => 'Human kills'),
                                 array('values' => $world_data, 'class' => 'world', 'key' => 'Misc deaths')));
    }

    function graph_winsOnMap($map_id)
    {
        $xo = 49.5;
        $yo = 59.5;
        $r = 40;

        global $db;
        $wins = $db->GetRow('SELECT mapstat_alien_wins,
                                    mapstat_human_wins,
                                    mapstat_ties + mapstat_draws AS ties
                             FROM map_stats WHERE mapstat_id = ?',
                             array($map_id));
        if ($wins['mapstat_alien_wins'] + $wins['mapstat_human_wins'] + $wins['ties'] > 0) {
            $alien = $wins['mapstat_alien_wins'];
            $human = $wins['mapstat_human_wins'];
            $tie   = $wins['ties'];
        } else {
            $alien = 0;
            $human = 0;
            $tie   = 0;
        }
        $total = $alien + $human + $tie;

        if ($total) {
            $sectors = array(graphlib_makeSector($xo, $yo, $r, 0,             $alien, $total, 'alien'),
                             graphlib_makeSector($xo, $yo, $r, $alien,        $tie,   $total, 'tied'),
                             graphlib_makeSector($xo, $yo, $r, $alien + $tie, $human, $total, 'human'));
        } else
            $sectors = array(graphlib_makeSector($xo, $yo, $r, 0, 1, 1, 'null'));

        echo <<<EOF
<svg width='200' height='120' version='1.1' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg'>
<defs>
EOF;

        foreach ($sectors as $sector)
        {
            if (array_key_exists('clip', $sector))
                echo $sector['clip'];
        }
        echo "</defs>\n<circle cx='$xo' cy='$yo' r='", $r + 1, "' class='filler' />\n";
        foreach ($sectors as $sector)
        {
            if (array_key_exists('drawing', $sector))
                echo $sector['drawing'];
        }

        if ($total)
        {
            if ($alien != $total && $human != $total && $tie != $total)
                echo "<line class='pie' x1='$xo' y1='$yo' x2='$xo' y2='", $yo - $r, "' />\n";
            if ($alien && $alien != $total) {
                $point = graphlib_calcSpoke($r, $alien, $total);
                echo "<line class='pie' x1='$xo' y1='$yo' x2='", $point[0] + $xo, "' y2='", $point[1] + $yo, "' />\n";
            }
            if ($tie && ($alien + $tie) != $total) {
                $point = graphlib_calcSpoke($r, $alien + $tie, $total);
                echo "<line class='pie' x1='$xo' y1='$yo' x2='", $point[0] + $xo, "' y2='", $point[1] + $yo, "' />\n";
            }

            $ap = round($alien * 100.0 / $total);
            $tp = round($tie   * 100.0 / $total);
            $hp = round($human * 100.0 / $total);
            echo <<<EOF
<rect x='105' y='17' width='10' height='10' class='alien box' /><text x='120' y='26' >Aliens</text><text class='percent' x='120' y='30' >$ap%</text>
<rect x='105' y='55' width='10' height='10' class='tied box'  /><text x='120' y='64' >Tied</text>  <text class='percent' x='120' y='68' >$tp%</text>
<rect x='105' y='93' width='10' height='10' class='human box' /><text x='120' y='102'>Humans</text><text class='percent' x='120' y='106'>$hp%</text>
EOF;
        }

        echo "</svg>\n";
    }

    function graph_mapBalanceBar($alien, $tie, $human)
    {
        $total = $alien + $tie + $human;

        echo <<<EOF
<svg width='402' height='30' version='1.1' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg'>
<defs></defs>
<line x1='101.5' y1='3' x2='101.5' y2='27' class='marker_minor' />
<line x1='201.5' y1='2' x2='201.5' y2='28' class='marker_major' />
<line x1='301.5' y1='3' x2='301.5' y2='27' class='marker_minor' />
<rect y='7' width='100%' height='16' class='filler' />
EOF;

        if ($total)
        {
            $w_a = $alien * 400.0 / $total;
            $w_b = $tie   * 400.0 / $total;
            $w_c = $human * 400.0 / $total;
            $x_b = $w_a + 1;
            $x_c = $x_b + $w_b;
            echo <<<EOF
<rect x='1'    y='8' height='14' width='$w_a' class='alien' />
<rect x='$x_b' y='8' height='14' width='$w_b' class='tied' />
<rect x='$x_c' y='8' height='14' width='$w_c' class='human' />
</svg>
EOF;
        }
        else
            echo <<<EOF
<rect x='.5' y='8' height='14' width='400' class='null' />
</svg>
EOF;
    }
} // not a bot
?>
