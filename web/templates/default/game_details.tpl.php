<?php include '__header__.tpl.php'; ?>

<section>
  <header>
    <h2 class="heading">Game #<?php echo $this->game_details['game_id']; ?> Summary</h2>
    <div class="headinglink"> ( <a href="game_log.php?game_id=<?php echo $this->game_details['game_id'] ?>">show game log</a> )</div>
  </header>

  <table>
    <colgroup>
      <col class="levelshot" />
      <col class="item" />
      <col />
    </colgroup>

    <thead>
      <tr><th colspan="3">Game info</th></tr>
    </thead>

    <tbody>
     <tr>
      <td rowspan="10" class="levelshot">
        <img class="levelshot" alt="<?php echo htmlspecialchars($this->map['game_map_name'],ENT_QUOTES); ?>" src="_levelshot.php?map_id=<?php echo ($this->game_details['game_map_id']); ?>" />
      </td>
      <td><strong>Map Name</strong></td>
      <td><strong><a href="map_details.php?map_id=<?php echo $this->game_details['game_map_id'] ; ?>"><?php echo replace_color_codes($this->map['game_map_name']); ?></a></strong></td>
     </tr>
     <tr>
      <td>Winner</td>
      <td><?php echo $this->game_details['game_winner']; ?></td>
     </tr>
     <tr>
      <td>Game time</td>
      <td><?php echo $this->game_details['game_length']; ?></td>
     </tr>
     <tr>
      <td>Date <small>(UTC)</small></td>
      <td><?php echo $this->game_details['game_timestamp']; ?></td>
     </tr>
    </tbody>
  </table>

  <table>
    <colgroup>
      <col class="playername" />
      <col />
      <col />
      <col />
      <col />
    </colgroup>

    <thead>
      <tr>
        <th>Player</th>
        <th>Score</th>
        <th>Kills</th>
        <th>Team Kills</th>
        <th>Deaths</th>
        <th>Time</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <th colspan="6" class="aliens-teamshader">Aliens (stage <?php if (!empty($this->game_details['game_stage_alien3'])) echo 3; elseif (!empty($this->game_details['game_stage_alien2'])) echo 2; else echo 1; ?>)</th>
      </tr>
      <?php $count = false; foreach ($this->players as $player) { ?>
        <?php if ($player['time_alien']) { ?>
      <tr class="list" >
        <td class="playername"><?php echo player_link($player['player_id'], $player['player_name']) ?></td>
        <td><?php echo $player['stats_score'] ?></td>
        <td><?php echo $player['stats_kills'] ?></td>
        <td><?php echo $player['stats_teamkills'] ?></td>
        <td><?php echo $player['stats_deaths'] ?></td>
        <td><?php echo $player['time_alien'] ?></td>
      </tr>
        <?php   $count = true;
              }
            }
            if (!$count) { ?>
        <tr>
          <td colspan="6">No players</td>
        </tr>
      <?php } ?>

      <tr>
        <th colspan="6" class="humans-teamshader">Humans (stage <?php if (!empty($this->game_details['game_stage_human3'])) echo 3; elseif (!empty($this->game_details['game_stage_human2'])) echo 2; else echo 1; ?>)</th>
      </tr>
      <?php $count = false; foreach ($this->players as $player) { ?>
        <?php if ($player['time_human']) { ?>
      <tr class="list" >
        <td class="playername"><?php echo player_link($player['player_id'], $player['player_name']) ?></td>
        <td><?php echo $player['stats_score'] ?></td>
        <td><?php echo $player['stats_kills'] ?></td>
        <td><?php echo $player['stats_teamkills'] ?></td>
        <td><?php echo $player['stats_deaths'] ?></td>
        <td><?php echo $player['time_human'] ?></td>
      </tr>
        <?php   $count = true;
              }
            }
            if (!$count) { ?>
        <tr>
          <td colspan="6">No players</td>
        </tr>
      <?php } ?>

      <tr>
        <th colspan="6" class="spectators-teamshader">Spectators</th>
      </tr>
      <?php $count = false; foreach ($this->players as $player) { ?>
        <?php if ($player['time_spec'] && !$player['time_human'] && !$player['time_alien']) { ?>
      <tr class="list" >
        <td class="playername" colspan="6"><?php echo player_link($player['player_id'], $player['player_name']) ?></td>
      </tr>
        <?php   $count = true;
              }
            }
            if (!$count) { ?>
        <tr>
          <td colspan="6">None</td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

  <table>
    <colgroup>
      <col />
    </colgroup>

    <thead>
      <tr>
        <th>Stats per minute</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td><?php graph_killsInGame($this->game_details['game_id']); ?></td>
      </tr>
    </tbody>
  </table>

</section>

 <?php include '__footer__.tpl.php'; ?>
