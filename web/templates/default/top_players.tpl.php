<?php include '__header__.tpl.php'; ?>

<div id="box">
  <h2>Top Players</h2>

  <table>
    <colgroup>
      <col class="id" />
      <col class="playernamewide" />
      <col class="data" />
      <col class="data" />
      <col class="data" />
      <col class="data" />
      <col class="data" />
      <col class="data" />
      <col class="data" />
      <col class="data" />
    </colgroup>

    <thead>
      <tr>
        <th title="Efficiency Rank"><?php echo custom_sort('#',          'rank'); ?></th>
        <th><?php echo custom_sort('Player',     'player'); ?></th>
        <th title="Total Score"><?php echo custom_sort('Score',      'score'); ?></th>
        <th title="Total Kills"><?php echo custom_sort('Kills',      'kills'); ?></th>
        <th title="Total Deaths"><?php echo custom_sort('Deaths',     'deaths'); ?></th>
        <th title="Total Team Kills"><?php echo custom_sort('TKs', 'team_kills'); ?></th>
        <th title="Player Efficiency Rating"><?php echo custom_sort('Efficiency', 'efficiency'); ?></th>
        <th title="Player Skill Rating"><?php echo custom_sort('Skill', 'skill'); ?></th>
        <th title="Player Skill Rating as Alien"><?php echo custom_sort('Skill Alien', 'skill_a'); ?></th>
        <th title="Player Skill Rating as Human"><?php echo custom_sort('Skill Human', 'skill_h'); ?></th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <td colspan="10">
          Pages: <?php echo $this->pagelister->GetHTML(); ?>
        </td>
      </tr>
    </tfoot>

    <tbody>
      <?php foreach ($this->top AS $player): ?>
        <tr class="list" >
          <td><?php echo $player['player_rank']; ?></td>
          <td class="playername"><?php echo player_link($player['player_id'], $player['player_name']); ?></td>
          <td><?php echo $player['player_score_total']; ?></td>
          <td><?php echo $player['player_kills']; ?></td>
          <td><?php echo $player['player_deaths']; ?></td>
          <td><?php echo $player['player_teamkills']; ?></td>
          <td><?php echo $player['player_total_efficiency']; ?></td>
          <?php skillTD($player['skill'], $player['skill_sigma']); ?>
          <?php skillTD($player['skill_a'], $player['skill_a_sigma']); ?>
          <?php skillTD($player['skill_h'], $player['skill_h_sigma']); ?>
        </tr>
      <?php endforeach; ?>

      <?php if (!count($this->top)): ?>
        <tr>
          <td colspan="10">No players yet</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
 </div>

 <?php include '__footer__.tpl.php'; ?>
