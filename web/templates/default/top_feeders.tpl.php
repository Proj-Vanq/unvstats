<?php include '__header__.tpl.php'; ?>

<section>
  <header>
    <h2>Top Feeders</h2>
  </header>

  <table>
    <colgroup>
      <col class="id" />
      <col class="playernamewide" />
      <col />
      <col />
    </colgroup>

    <thead>
      <tr>
        <th title="Feed Rank"><?php echo custom_sort('#',      'rank'); ?></th>
        <th><?php echo custom_sort('Player', 'player'); ?></th>
        <th title="Average Kills to Enemy per Round"><?php echo custom_sort('AVG Kills',   'average_kills'); ?></th>
        <th title="Average Deaths by Enemy per Round"><?php echo custom_sort('AVG Deaths', 'average_deaths'); ?></th>
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
      <?php foreach ($this->top AS $player): ?>
        <tr class="list" >
          <td><?php echo $player['player_rank']; ?></td>
          <td class="playername"><?php echo player_link($player['player_id'], $player['player_name']); ?></td>
          <td><?php echo $player['average_kills_to_enemy']; ?></td>
          <td><?php echo $player['average_deaths_by_enemy']; ?></td>
        </tr>
      <?php endforeach; ?>

      <?php if (!count($this->top)): ?>
        <tr class="emptylist">
          <td colspan="4">No players yet, or not enough games played</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
 </section>

 <?php include '__footer__.tpl.php'; ?>

