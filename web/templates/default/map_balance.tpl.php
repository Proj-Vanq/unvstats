<?php include '__header__.tpl.php'; ?>

<section>
  <header>
    <h2>Map Balance by Wins</h2>
  </header>

  <table class="mapbalance">
    <colgroup>
      <col class="item" />
      <col class="balancebar" />
      <col class="data" />
    </colgroup>

    <thead>
      <tr>
        <th>Map</th>
        <th></th>
        <th>Games</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($this->maps_by_wins AS $map): ?>
        <tr>
          <td>
            <a href="map_details.php?map_id=<?php echo $map['map_id'] ; ?>"><?php echo replace_color_codes($map['map_text_name']); ?></a>
          </td>
          <td title="Alien Wins: <?php echo $map['mapstat_alien_wins']; ?>, Human Wins: <?php echo $map['mapstat_human_wins']; ?>, Ties: <?php echo $map['ties'];?>">
            <?php graph_mapBalanceBar($map['mapstat_alien_wins'], $map['ties'], $map['mapstat_human_wins']); ?>
          </td>
          <td><?php echo $map['mapstat_alien_wins'] + $map['mapstat_human_wins'] + $map['ties']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <table class="legend">
    <colgroup>
      <col class="item"/>
    </colgroup>

    <thead>
      <tr>
        <th>Legend</th>
      </tr>
    </thead>

    <tbody>
        <tr><td class="aliens">Alien Wins</td></tr>
        <tr><td class="tied">Ties / Draws</td></tr>
        <tr><td class="humans">Human Wins</td></tr>
    </tbody>
  </table>
</section>

<section>
  <header>
    <h2>Map Balance by Kills</h2>
  </header>

  <table class="mapbalance">
    <colgroup>
      <col class="item" />
      <col class="balancebar" />
      <col class="data" />
    </colgroup>

    <thead>
      <tr>
        <th>Map</th>
        <th></th>
        <th>Kills</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($this->maps_by_kills AS $map): ?>
        <tr>
          <td>
            <a href="map_details.php?map_id=<?php echo $map['map_id'] ; ?>"><?php echo replace_color_codes($map['map_text_name']); ?></a>
          </td>
          <td title="Alien Kills: <?php echo $map['mapstat_alien_kills'];?>, Human Kills: <?php echo $map['mapstat_human_kills']; ?>">
            <?php graph_mapBalanceBar($map['mapstat_alien_kills'], 0, $map['mapstat_human_kills']); ?>
          </td>
          <td><?php echo $map['mapstat_alien_kills'] + $map['mapstat_human_kills']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <table class="legend">
    <colgroup>
      <col class="item"/>
    </colgroup>

    <thead>
      <tr>
        <th>Legend</th>
      </tr>
    </thead>

    <tbody>
        <tr><td class="aliens">Alien Kills</td></tr>
        <tr><td class="humans">Human Kills</td></tr>
    </tbody>
  </table>
</section>

<section>
  <header>
    <h2>Map Balance by Deaths</h2>
  </header>

  <table>
    <colgroup>
      <col class="item" />
      <col class="balancebar" />
      <col class="data" />
    </colgroup>

    <thead>
      <tr>
        <th>Map</th>
        <th></th>
        <th>Deaths</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($this->maps_by_deaths AS $map): ?>
        <tr>
          <td>
            <a href="map_details.php?map_id=<?php echo $map['map_id'] ; ?>"><?php echo replace_color_codes($map['map_text_name']); ?></a>
          </td>
          <td title="Alien Deaths: <?php echo $map['mapstat_alien_deaths'];?>, Human Deaths: <?php echo $map['mapstat_human_deaths']; ?>">
            <?php graph_mapBalanceBar($map['mapstat_alien_deaths'], 0, $map['mapstat_human_deaths']); ?>
          </td>
          <td><?php echo $map['mapstat_alien_deaths'] + $map['mapstat_human_deaths']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <table class="legend">
    <colgroup>
      <col class="item"/>
    </colgroup>

    <thead>
      <tr>
        <th>Legend</th>
      </tr>
    </thead>

    <tbody>
        <tr><td class="aliens">Alien Deaths</td></tr>
        <tr><td class="humans">Human Deaths</td></tr>
    </tbody>
  </table>
</section>

 <?php include '__footer__.tpl.php'; ?>
