<?php include '__header__.tpl.php'; ?>

<section>
  <header>
    <h2>Most Active Players</h2>
  </header>

  <table>
    <colgroup>
      <col class="id" />
      <col class="playernamewide" />
      <col />
      <col />
      <col class="datawide" />
      <col />
      <col />
      <col />
      <col />
      <col />
    </colgroup>

    <thead>
      <tr>
        <th title="Game-time factor Rank"><?php echo custom_sort('#',          'rank'); ?></th>
        <th><?php echo custom_sort('Player',     'player'); ?></th>
        <th><?php echo custom_sort('Kills',      'kills'); ?></th>
        <th><?php echo custom_sort('Deaths',     'deaths'); ?></th>
        <th><?php echo custom_sort('Efficiency', 'efficiency'); ?></th>
        <th><?php echo custom_sort('Games',      'games'); ?></th>
        <th title="Game-time factor"><?php echo custom_sort('GFT',        'gametimefactor'); ?></th>
        <th><?php echo custom_sort('Skill',      'skill'); ?></th>
        <th><?php echo custom_sort('Skill Alien','skill_a'); ?></th>
        <th><?php echo custom_sort('Skill Human','skill_h'); ?></th>
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
          <td><?php echo $player['player_kills']; ?></td>
          <td><?php echo $player['player_deaths']; ?></td>
          <td><?php echo $player['player_total_efficiency']; ?></td>
          <td><?php echo $player['player_games_played']; ?></td>
          <td><?php echo $player['player_game_time_factor']; ?></td>
          <?php skillTD($player['skill'], @$player['skill_sigma']); ?>
          <?php skillTD($player['skill_a'], @$player['skill_a_sigma']); ?>
          <?php skillTD($player['skill_h'], @$player['skill_h_sigma']); ?>
        </tr>
      <?php endforeach; ?>

      <?php if (!count($this->top)): ?>
        <tr class="emptylist">
          <td colspan="10">No players yet</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>

 <?php include '__footer__.tpl.php'; ?>
