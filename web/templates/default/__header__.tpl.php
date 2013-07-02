<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <title>Unvstats – <?php echo replace_color_codes(SERVER_NAME); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="Publisher" content="DASPRiD's" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->css_file(); ?>" />
<?php
/* site hooks */
function site_hook($hook)
{
  $hook = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $hook . '.php';
  if (is_readable($hook))
    include $hook;
}

if (is_readable(dirname($_SERVER['SCRIPT_FILENAME']) . '/site-style.css'))
  echo '<link rel="stylesheet" type="text/css" href="' . dirname($_SERVER['REQUEST_URI']) . '/site-style.css" />';

site_hook('site-style');
?>
  </head>
  <body>
<?php site_hook('site-top'); ?>
    <div id="header">
      <form class="search" method="get" accept-charset="utf-8" action="search_player.php">
        <fieldset>
          <label for="query">Player search:</label>
          <input type="text" name="query" id="query" value="<?php if (isset($_GET['query'])): ?><?php echo htmlspecialchars($_GET['query'],ENT_QUOTES) ?><?php endif; ?>" />
          <input type="submit" value="Search" />
        </fieldset>
      </form>

      <h1><img src="images/unvstats.png" alt="Unvstats " /><span class="for"><br /></span><span id="serverName"><?php echo replace_color_codes(SERVER_NAME); ?></span></h1>
    </div>
    <div class="menu">
<?php
function menu_link($page, $title)
{
  $p = is_array($page) ? $page[0] : $page;
  $a = substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1);
  if (substr($a, -4) == '.php')
    $a = substr($a, 0, -4);
  if (is_array($page) ? in_array($a, $page) : ($a == $page))
    echo "<li id=\"currentPage\"><a href=\"$p.php\">$title</a></li>";
  else
    echo "<li><a href=\"$p.php\">$title</a></li>";
}

site_hook('site-menu');
?>
      <div>
        <ul class="menu">
          <?php menu_link(array('index', ''),    'Overview'); ?>
          <?php menu_link('top_players',         'Top Players'); ?>
          <?php menu_link('top_feeders',         'Feeders'); ?>
          <?php menu_link('top_teamkillers',     'Team Killers'); ?>
          <?php menu_link('most_active_players', 'Most Active Players'); ?>
          <?php menu_link('votes',               'Votes'); ?>
          <?php menu_link('most_played_maps',    'Maps'); ?>
          <?php menu_link('map_balance',         'Balance'); ?>
          <?php menu_link('games',               'Games'); ?>
        </ul>
      </div>
    </div>

    <div id="box">
      <div class="container">
        <?php include '__pagelister__.php'; ?>
        <?php require '__skill__.php'; ?>
