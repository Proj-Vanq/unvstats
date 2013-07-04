<?php include '__header__.tpl.php'; ?>

<section>
  <header>
    <h2>Voting</h2>
  </header>

  <div class="split-table">
    <table>
      <colgroup>
        <col class="playername" />
        <col class="data" />
        <col />
      </colgroup>

      <thead>
        <tr>
          <th >Most Voted Maps</th>
          <th >Pass</th>
          <th >Fail</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($this->map_votes)) { ?>
          <tr class="emptylist"><td colspan="3">No votes</td></tr>
        <?php } else foreach ($this->map_votes as $map) { ?>
        <tr>
          <td><a href="map_details.php?map_id=<?php echo $map['map_id'] ; ?>"><?php echo replace_color_codes($map['map_text_name']); ?></a></td>
          <td><?php echo $map['count_pass']; ?></td>
          <td><?php echo $map['count_fail']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table><!-- --><table>
      <colgroup>
        <col class="playername" />
        <col class="data" />
        <col />
      </colgroup>

      <thead>
        <tr>
          <th >Most Skipped Maps</th>
          <th >Pass</th>
          <th >Fail</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($this->map_skips)) { ?>
          <tr class="emptylist"><td colspan="3">No votes</td></tr>
        <?php } else foreach ($this->map_skips as $map) { ?>
        <tr>
          <td><a href="map_details.php?map_id=<?php echo $map['map_id'] ; ?>"><?php echo replace_color_codes($map['map_text_name']); ?></a></td>
          <td><?php echo $map['count_pass']; ?></td>
          <td><?php echo $map['count_fail']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <div class="split-table">
    <table>
      <colgroup>
        <col class="playername" />
        <col class="data" />
        <col />
      </colgroup>

      <thead>
        <tr>
          <th >Most Kicked Player</th>
          <th >Pass</th>
          <th >Fail</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($this->kick_votes)) { ?>
          <tr class="emptylist"><td colspan="3">No votes</td></tr>
        <?php } else foreach ($this->kick_votes as $kick) { ?>
        <tr>
          <td class="playername"><?php echo player_link($kick['player_id'], $kick['player_name']); ?></td>
          <td><?php echo $kick['count_pass']; ?></td>
          <td><?php echo $kick['count_fail']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table><!-- --><table>
      <colgroup>
        <col class="playername" />
        <col class="data" />
        <col />
      </colgroup>

      <thead>
        <tr>
          <th >Most Muted Player</th>
          <th >Pass</th>
          <th >Fail</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($this->mute_votes)) { ?>
          <tr class="emptylist"><td colspan="3">No votes</td></tr>
        <?php } else foreach ($this->mute_votes as $mute) { ?>
        <tr>
          <td class="playername"><?php echo player_link($mute['player_id'], $mute['player_name']); ?></td>
          <td><?php echo $mute['count_pass']; ?></td>
          <td><?php echo $mute['count_fail']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <div class="split-table">
    <table>
      <colgroup>
        <col class="playername" />
        <col />
      </colgroup>

      <thead>
        <tr>
          <th >Kick-Happy Players</th>
          <th >Called Kicks</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($this->kick_happy)) { ?>
          <tr class="emptylist"><td colspan="3">No votes</td></tr>
        <?php } else foreach ($this->kick_happy as $kick) { ?>
        <tr>
          <td class="playername"><?php echo player_link($kick['player_id'], $kick['player_name']); ?></td>
          <td><?php echo $kick['votes']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table><!-- --><table>
      <colgroup>
        <col class="playername" />
        <col />
      </colgroup>

      <thead>
        <tr>
          <th >Mute-Happy Players</th>
          <th >Called Mutes</th>
        </tr>
      </thead>

      <tbody>
        <?php if (empty($this->mute_happy)) { ?>
          <tr class="emptylist"><td colspan="3">No votes</td></tr>
        <?php } else foreach ($this->mute_happy as $mute) { ?>
        <tr>
          <td class="playername"><?php echo player_link($mute['player_id'], $mute['player_name']); ?></td>
          <td><?php echo $mute['votes']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <table>
    <colgroup>
      <col class="playername" />
      <col class="data" />
      <col class="data" />
      <col class="data" />
      <col />
    </colgroup>

    <thead>
      <tr>
        <th >Vote Summary by Type</th>
        <th >Called</th>
        <th >Passed</th>
        <th >Failed</th>
        <th >Success Rate</th>
      </tr>
    </thead>

    <tbody>
      <?php $total = 0; $pass = 0; ?>
      <?php foreach ($this->summary AS $vote): ?>
      <tr>
          <td><?php echo $vote['vote_type']; ?></td>
          <td><?php echo $vote['count']; $total += $vote['count']; ?></td>
          <td><?php echo $vote['count_pass']; $pass += $vote['count_pass']; ?></td>
          <td><?php echo ($vote['count'] - $vote['count_pass']); ?></td>
          <td><?php echo (int)(100 * $vote['count_pass'] / $vote['count']); ?> %</td>
      </tr>
      <?php endforeach; ?>
      <tr class="spacer"><td colspan="5"></td></tr>
      <tr>
        <td>Totals</td>
        <td><?php echo $total; ?></td>
        <td><?php echo $pass; ?></td>
        <td><?php echo ($total - $pass); ?></td>
        <td><?php echo $total ? '' . (int)(100 * $pass / $total) . ' %' : '-'; ?></td>
      </tr>
    </tbody>
  </table>

</section>

<?php include '__footer__.tpl.php'; ?>
