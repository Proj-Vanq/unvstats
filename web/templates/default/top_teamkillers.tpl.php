<?php include '__header__.tpl.php'; ?>

<section>
  <header>
    <h2>Top Team Killers</h2>
  </header>

  <table>
    <colgroup>
      <col class="id" />
      <col class="playernamewide" />
      <col />
      <col />
      <col />
    </colgroup>

    <thead>
      <tr>
        <th title="Team Kill Rank"><?php echo custom_sort('#',      'rank'); ?></th>
        <th><?php echo custom_sort('Player', 'player'); ?></th>
        <th title="Average Kills to Enemy per Round"><?php echo custom_sort('AVG Kills',     'average_kills'); ?></a></th>
        <th title="Average Deaths by Enemy per Round"><?php echo custom_sort('AVG Deaths',   'average_deaths'); ?></th>
        <th title="Average Kills to Team per Round"><?php echo custom_sort('AVG Team Kills', 'average_team_kills'); ?></th>
      </tr>
    </thead>

    <tfoot>
      <tr>
        <td colspan="5">
          Pages: <?php echo $this->pagelister->GetHTML(); ?>
        </td>
      </tr>
    </tfoot>

    <tbody>
      <?php foreach ($this->top AS $player): ?>
        <tr class="list" >
          <td><?php echo $player['player_rank']; ?></td>
          <td class="playername"><?php echo player_link($player['player_id'], $player['player_name']); ?></td>
          <td><?php echo $player['average_kills_to_enemy']; ?></td>
          <td><?php echo $player['average_deaths_by_enemy']; ?></td>
          <td><?php echo $player['average_kills_to_team']; ?></td>
        </tr>
      <?php endforeach; ?>

      <?php if (!count($this->top)): ?>
        <tr class="emptylist">
          <td colspan="5">No players yet</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
 </section>

 <?php include '__footer__.tpl.php'; ?>
