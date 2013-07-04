<?php include '__header__.tpl.php'; ?>

<div>
  <h2>Server Status</h2>

  <?php if (isset($this->server_status)): ?>

  <table>
    <colgroup>
      <col class="playername" />
      <col />
      <col />
      <col class="playername" />
      <col />
      <col />
    </colgroup>

    <thead>
      <tr>
        <th colspan="3">Aliens</th>
        <th colspan="3">Humans</th>
      </tr>
    </thead>

    <tbody>
      <?php if ($this->server_status === false): ?>
        <tr>
          <td colspan="6">Server not reachable</td>
        </tr>
      <?php elseif (!count($this->server_status['aliens']) && !count($this->server_status['humans']) && !count($this->server_status['specs'])): ?>
        <tr>
          <td colspan="6">No players online</td>
        </tr>
      <?php else: ?>
        <tr>
          <th>Player</th>
          <th>Score</th>
          <th>Ping</th>
          <th>Player</th>
          <th>Score</th>
          <th>Ping</th>
        </tr>

        <?php $rows = max(count($this->server_status['aliens']), count($this->server_status['humans'])); ?>
        <?php for ($i=0; $i<$rows; $i++): ?>
          <?php
          $alien = (isset($this->server_status['aliens'][$i]) ? $this->server_status['aliens'][$i]: null);
          $human = (isset($this->server_status['humans'][$i]) ? $this->server_status['humans'][$i]: null);
          ?>
          <tr>
            <?php if (!is_null($alien)): ?>
              <td class="playername"><?php echo replace_color_codes($alien['name']); ?></td>
              <td><?php echo $alien['kills']; ?></td>
              <td><?php echo $alien['ping']; ?></td>
            <?php else: ?>
              <td colspan="3">&nbsp;</td>
            <?php endif; ?>

            <?php if (!is_null($human)): ?>
              <td class="playername"><?php echo replace_color_codes($human['name']); ?></td>
              <td><?php echo $human['kills']; ?></td>
              <td><?php echo $human['ping']; ?></td>
            <?php else: ?>
              <td colspan="3">&nbsp;</td>
            <?php endif; ?>
          </tr>
        <?php endfor; ?>
      <?php endif; ?>

      <?php if (count($this->server_status['specs'])): ?>
        <tr>
          <th colspan="6">Spectators</th>
        </tr>

        <?php foreach ($this->server_status['specs'] AS $spec): ?>
          <tr>
            <td colspan="6"><?php echo replace_color_codes($spec['name']); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <table>
    <colgroup>
      <col class="levelshot" />
      <col class="item" />
      <col />
      <col class="chart" />
    </colgroup>

    <thead>
      <tr>
        <th colspan="4">Running map</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td rowspan="5" class="levelshot">
          <img class="levelshot" alt="<?php echo htmlspecialchars($this->running_map['map_text_name'],ENT_QUOTES); ?>" src="_levelshot.php?map_id=<?php echo ($this->running_map['map_id']); ?>" />
        </td>
        <td><strong>Map Name</strong></td>
        <td><strong><a href="map_details.php?map_id=<?php echo $this->running_map['map_id'] ; ?>"><?php echo replace_color_codes($this->running_map['map_text_name']); ?></a></strong></td>
        <td rowspan="5" title="Winners in Games">
          <?php graph_winsOnMap($this->running_map['map_id']); ?>
        </td>
      </tr>
      <tr>
        <td>Times Played</td>
        <td><?php echo $this->running_map['mapstat_games']; ?></td>
      </tr>
      <tr>
        <td>Alien Wins</td>
        <td><?php echo $this->running_map['mapstat_alien_wins']; ?></td>
      </tr>
      <tr>
        <td>Human Wins</td>
        <td><?php echo $this->running_map['mapstat_human_wins']; ?></td>
      </tr>
      <tr>
        <td>Ties</td>
        <td><?php echo $this->running_map['ties']; ?></td>
      </tr>
    </tbody>
  </table>

  <?php else: ?>
    <blockquote>
      <i>No response from server</i>
    </blockquote>
  <?php endif ?>

  <h2>Overview</h2>

  <table>
    <colgroup>
      <col class="item" />
      <col />
    </colgroup>

    <thead>
      <tr>
        <th colspan="2">General data</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td>Games Played</td>
        <td><?php echo $this->overview['games']; ?></td>
      </tr>
      <tr>
        <td>Alien Wins</td>
        <td><?php echo $this->overview['alien_wins']; ?></td>
      </tr>
      <tr>
        <td>Human Wins</td>
        <td><?php echo $this->overview['human_wins']; ?></td>
      </tr>
      <tr>
        <td>Tied Matches</td>
        <td><?php echo $this->overview['ties']; ?></td>
      </tr>
    </tbody>
  </table>

  <table>
    <colgroup>
      <col class="item" />
      <col />
    </colgroup>

    <thead>
      <tr>
        <th colspan="2">Popular Players</th>
      </tr>
    </thead>

    <tbody>
<?php if (isset($this->overview['top_player']['player_id'])) { ?>
      <tr>
        <td>Top Player</td>
        <td><span class="playername"><?php echo player_link($this->overview['top_player']['player_id'], $this->overview['top_player']['player_name']); ?></span> (Efficiency: <?php echo $this->overview['top_player']['player_total_efficiency']; ?>)</td>
      </tr>
<?php } ?>
<?php if (isset($this->overview['top_feeder']['player_id'])) { ?>
      <tr>
        <td>Top Feeder</td>
        <td><span class="playername"><?php echo player_link($this->overview['top_feeder']['player_id'], $this->overview['top_feeder']['player_name']); ?></span> (Average Deaths: <?php echo $this->overview['top_feeder']['average_deaths_by_enemy']; ?>)</td>
      </tr>
<?php } ?>
<?php if (isset($this->overview['top_teamkiller']['player_id'])) { ?>
      <tr>
        <td>Top Teamkiller</td>
        <td><span class="playername"><?php echo player_link($this->overview['top_teamkiller']['player_id'], $this->overview['top_teamkiller']['player_name']); ?></span> (Average Team Kills: <?php echo $this->overview['top_teamkiller']['average_kills_to_team']; ?>)</td>
      </tr>
<?php } ?>
<?php if (isset($this->overview['top_score']['player_id'])) { ?>
      <tr>
        <td>Top Score</td>
        <td><span class="playername"><?php echo player_link($this->overview['top_score']['player_id'], $this->overview['top_score']['player_name']); ?></span> (Score: <?php echo $this->overview['top_score']['stats_score']; ?>)</td>
      </tr>
<?php } ?>
<?php if (isset($this->overview['top_skill']['player_id'])) { ?>
      <tr>
        <td>Top Skill</td>
        <td><span class="playername"><?php echo player_link($this->overview['top_skill']['player_id'], $this->overview['top_skill']['player_name']); ?></span> (Skill: <?php skill($this->overview['top_skill']['skill'], $this->overview['top_skill']['skill_sigma']); ?>)</td>
      </tr>
<?php } ?>
<?php if (isset($this->overview['most_active_player']['player_id'])) { ?>
      <tr>
        <td>Most Active Player</td>
        <td><span class="playername"><?php echo player_link($this->overview['most_active_player']['player_id'], $this->overview['most_active_player']['player_name']); ?></span> (Game-time factor: <?php echo $this->overview['most_active_player']['player_game_time_factor']; ?>)</td>
      </tr>
<?php } ?>
    </tbody>
  </table>

  <table>
    <colgroup>
      <col class="levelshot" />
      <col class="item" />
      <col />
      <col class="chart" />
    </colgroup>

    <thead>
      <tr>
        <th colspan="4">Most Played Map</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td rowspan="5" class="levelshot">
          <img class="levelshot" alt="<?php echo htmlspecialchars($this->overview['most_played_map']['map_text_name'],ENT_QUOTES); ?>" src="_levelshot.php?map_id=<?php echo ($this->overview['most_played_map']['map_id']); ?>" />
        </td>
        <td><strong>Map Name</strong></td>
        <td><strong><a href="map_details.php?map_id=<?php echo $this->overview['most_played_map']['map_id'] ; ?>"><?php echo replace_color_codes($this->overview['most_played_map']['map_text_name']); ?></a></strong></td>
        <td rowspan="5" title="Winners in Games">
          <?php graph_winsOnMap($this->overview['most_played_map']['map_id']); ?>
        </td>
      </tr>
      <tr>
        <td>Times Played</td>
        <td><?php echo $this->overview['most_played_map']['mapstat_games']; ?></td>
      </tr>
      <tr>
        <td>Alien Wins</td>
        <td><?php echo $this->overview['most_played_map']['mapstat_alien_wins']; ?></td>
      </tr>
      <tr>
        <td>Human Wins</td>
        <td><?php echo $this->overview['most_played_map']['mapstat_human_wins']; ?></td>
      </tr>
      <tr>
        <td>Ties</td>
        <td><?php echo $this->overview['most_played_map']['ties']; ?></td>
      </tr>
    </tbody>
  </table>

 <div class="update">Last update: <?php echo $this->state['log_timestamp']; ?> <small>UTC</small></div>

</div>

<?php include '__footer__.tpl.php'; ?>
