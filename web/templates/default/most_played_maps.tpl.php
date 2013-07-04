<?php include '__header__.tpl.php'; ?>

<section>
  <header>
    <h2><?php if ($this->order == 'alienwins'): echo "Maps by Alien Wins"; elseif ($this->order == 'humanwins'): echo "Maps by Human Wins"; elseif ($this->order == 'mapname'): echo "Maps by Name"; else: echo "Most Played Maps"; endif; ?></h2>
    <div class="headinglink">
      Sort by: <a href="most_played_maps.php">Most Played</a> | <a href="most_played_maps.php?sort=alienwins">Alien Wins</a> | <a href="most_played_maps.php?sort=humanwins">Human Wins</a> | <a href="most_played_maps.php?sort=mapname">Map Name</a>
    </div>
  </header>

  <table>
    <colgroup>
      <col class="levelshot" />
      <col class="item" />
      <col />
      <col class="chart" />
    </colgroup>

    <thead>
      <tr>
        <th>Levelshot</th>
        <th colspan="2">Data</th>
        <th>Winners in Games</th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <td colspan="4">
          Pages: <?php echo $this->pagelister->GetHTML(); ?>
        </td>
      </tr>
    </tfoot>

    <tbody>
      <?php $pos = 1; ?>
      <?php foreach ($this->top AS $game): ?>
        <?php if ($pos != 1): ?>
          <tr class="spacer"><td colspan="4"></td></tr>
        <?php endif; ?>
        <tr>
          <td rowspan="5" class="levelshot">
            <img class="levelshot" alt="<?php echo htmlspecialchars($game['map_name'],ENT_QUOTES); ?>" src="_levelshot.php?map_id=<?php echo ($game['map_id']); ?>" />
          </td>
          <td><strong>Map Name</strong></td>
          <td><strong><a href="map_details.php?map_id=<?php echo $game['map_id'] ; ?>"><?php echo replace_color_codes($game['map_text_name']); ?></a></strong></td>
          <td rowspan="5">
            <img width="200" height="120" alt="Winners in Games" src="_graph.php?type=wins_in_game&amp;map_id=<?php echo ($game['map_id']); ?>" />
          </td>
        </tr>
        <tr>
          <?php if($this->order == "mapname"): ?>
            <td>Simple Name</td>
            <td><?php echo $game['map_name']; ?></td>
          <?php else: ?>
            <td>Rank</td>
            <td><?php echo $game['map_rank']; ?></td>
          <?php endif; ?>
        </tr>
        <tr>
          <td>Times Played</td>
          <td><?php echo $game['mapstat_games']; ?></td>
        </tr>
        <tr>
          <td>Alien Wins</td>
          <td><?php echo $game['mapstat_alien_wins']; ?></td>
        </tr>
        <tr>
          <td>Human Wins</td>
          <td><?php echo $game['mapstat_human_wins']; ?></td>
        </tr>
        <?php $pos++; ?>
      <?php endforeach; ?>

      <?php if (!count($this->top)): ?>
        <tr>
          <td colspan="4">No games played yet</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>

 <?php include '__footer__.tpl.php'; ?>
