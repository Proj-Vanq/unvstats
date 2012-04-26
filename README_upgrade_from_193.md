Upgrading from 1.9.3 to 2.2.0
=============================

**Note:** This file is written using the Markdown markup. To view the formatted result, visit the address https://github.com/ppetr/tremstats/blob/master/README_upgrade_193_to_200.md at the project repository.

**WARNING: Before taking any steps, backup your database and your working version of TremStats! I take no responsibility for any damage or loss of your data.**

------------------------------------------------------------------------

The only thing needed for upgrading is to create a new database table `skill` and a new view `skill_last`. On the next run the parser will detect that the skills have not been computed yet, and will automatically examine the whole history stored in the database and compute the skills of the players based on all the games in your database. __This means that after upgrading the computed skills will reflect all your stored history.__

To create the table and the view, execute the following SQL statements in your database (if you're viewing the unformatted text file, ignore the lines starting with \`\`\`, they are just Markdown delimiters for the SQL code):

```sql
CREATE TABLE `skill` (
  `skill_id` int(11) unsigned NOT NULL auto_increment,
  `skill_player_id` int(11) unsigned NOT NULL,
  `skill_game_id` int(11) unsigned NOT NULL default '0',
  `skill_mu` double precision NOT NULL,
  `skill_sigma` double precision NOT NULL,
  `skill_alien_mu` double precision NOT NULL,
  `skill_alien_sigma` double precision NOT NULL,
  `skill_human_mu` double precision NOT NULL,
  `skill_human_sigma` double precision NOT NULL,
  PRIMARY KEY  (`skill_id`),
  KEY `skill_player_game` (`skill_player_id`, `skill_game_id`),
  KEY `skill_game_player` (`skill_game_id`, `skill_player_id`),
  KEY `skill_mu` (`skill_mu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE VIEW `skill_last` AS
  SELECT t.skill_id, t.skill_player_id, t.skill_game_id, 
        t.skill_mu, t.skill_sigma,
        t.skill_alien_mu, t.skill_alien_sigma,
        t.skill_human_mu, t.skill_human_sigma
    FROM skill t
   WHERE t.skill_game_id IN
     ( SELECT MAX(s.skill_game_id) FROM skill s
        WHERE s.skill_player_id = t.skill_player_id )
;
```
