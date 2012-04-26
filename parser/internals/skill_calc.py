# -*- coding: utf-8 -*-

# Main imports
import math
# External libraries
from externals.progressbar import ProgressBar, Percentage, Bar, ETA
# The trueskill module is loaded dynamically

""" Class: Skills """
class Skills:
    skillModuleName = "internals.trueskill.trueskill"
    initial_mu = 500
    initial_sigma = initial_mu / 3.0

    """ Init Skills """
    def Main(self, dbc):
        # Localize dbc
        self.dbc = dbc

        # Try to load the skill module
        self.skillModule = loadModule(self.skillModuleName, "The skill module cannot be loaded.")
        if self.skillModule == None:
            return
        # ... and initialize it
        self.skillModule.INITIAL_MU = self.initial_mu
        self.skillModule.INITIAL_SIGMA = self.initial_sigma
        self.skillModule.SetParameters()

        # Update skills -----------------------------------
        #self.dbc.execute("""SELECT MAX(g) FROM (SELECT GREATEST(COALESCE(p.`player_first_game_id`, 0), COALESCE(t.`gid`,0)) AS g FROM `players` p LEFT JOIN (SELECT MAX(`skill_game_id`) AS gid, skill_player_id FROM `skill`) AS t ON p.`player_id` = t.`skill_player_id`) AS t1""");
        # Find the least game which is missing skill stats.
        self.dbc.execute("""SELECT MIN(stats_game_id) FROM per_game_stats p WHERE NOT EXISTS (SELECT * FROM skill t WHERE p.stats_player_id = t.skill_player_id AND p.stats_game_id = t.skill_game_id)""");
        result = self.dbc.fetchone()
        ts_last_game = result[0]

        self.dbc.execute("""SELECT game_id, game_length, game_winner FROM games WHERE game_id > %s""", ts_last_game);
        games = self.dbc.fetchall();
        print "Last game with computed skills is %s, deleting newer stats and recomputing %s games." % (ts_last_game, len(games));

        self.dbc.execute("""DELETE FROM `skill` WHERE `skill_game_id` > %s""", ts_last_game);

        progress(games, lambda row: self.skillStats(self.dbc, row[0], totalSeconds(row[1]), row[2] == 'aliens', row[2] == 'humans'))


    def skillStats(self, dbc, game_id, game_time, asWon, hsWon):
        #print "--- Updating stats for game %s that took %s (humans won: %s)." % (game_id, game_time, hs)
        halfgame = game_time / 2
        # For each player select the last computed skill before the given game
        players = []
        self.dbc.execute("""
            SELECT p.stats_player_id, p.stats_time_alien, p.stats_time_human,
                COALESCE(t.skill_mu, %s) AS mu, COALESCE(t.skill_sigma, %s) AS sigma,
                COALESCE(t.skill_alien_mu, %s) AS mu_a, COALESCE(t.skill_alien_sigma, %s) AS sigma_a,
                COALESCE(t.skill_human_mu, %s) AS mu_h, COALESCE(t.skill_human_sigma, %s) AS sigma_h
            FROM per_game_stats p
              LEFT JOIN skill t ON t.skill_game_id IN (SELECT MAX(s.skill_game_id) FROM skill s WHERE s.skill_player_id = t.skill_player_id AND s.skill_game_id < %s) AND t.skill_player_id = p.stats_player_id
              WHERE p.stats_game_id = %s
            """, (self.initial_mu, self.initial_sigma,
                    self.initial_mu, self.initial_sigma,
                    self.initial_mu, self.initial_sigma,
                    game_id, game_id));
        # self.dbc.execute("""SELECT ... FROM per_game_stats p WHERE p.stats_game_id = %s""", game_id);
        for row in self.dbc.fetchall():
            player = Player( Skill(row[3], row[4])
                           , Skill(row[5], row[6])
                           , Skill(row[7], row[8])
                           )
            player.id = row[0]
            if row[1] > halfgame: # Alien at least 1/2 of the game time.
                player.team = player.alien
                if asWon:
                    player.rank(1)
                else:
                    player.rank(2)
            elif row[2] > halfgame: # Human at least 1/2 of the game time.
                player.team = player.human
                if hsWon:
                    player.rank(1)
                else:
                    player.rank(2)
            else:
                continue # disregard this player - didn't play long enough
            players.append(player)

        if (len(players) < 2):
            return # not enough players
        # Perform the computation
        try:
            # Adjust the overall skill:
            self.skillModule.AdjustPlayers(map(lambda p: p.total, players))
            # Adjust the skill corresponding to the team each player was in:
            self.skillModule.AdjustPlayers(map(lambda p: p.team, players))
        except Exception as e:
            print "Recomputation for game %s failed, please report to the develper.\n%s" % (game_id, e)
        # Update the database
        for player in players:
            #print "Player %s with skill %s/%s and rank %s." % (player.id, player.skill[0], player.skill[1], player.rank)
            self.dbc.execute("""INSERT INTO `skill`
              (`skill_player_id`, `skill_game_id`,
               `skill_mu`, `skill_sigma`,
               `skill_alien_mu`, `skill_alien_sigma`,
               `skill_human_mu`, `skill_human_sigma`)
              VALUES (%s, %s, %s, %s, %s, %s, %s, %s)""",
              (player.id, game_id, 
                  player.total.skill[0], player.total.skill[1],
                  player.alien.skill[0], player.alien.skill[1],
                  player.human.skill[0], player.human.skill[1] )
            )

def loadModule(name, msg):
    try:
        return __import__(name, fromlist='*')
    except ImportError, e:
        print "%s\n%s" % (msg, str(e.args))
        return None

def totalSeconds(td):
    return td.seconds + td.days * 24 * 3600

def progress(lst, fn):
    # Start the progressbar
    l = len(lst)
    try:
        pbar = ProgressBar(l, [Percentage(), ' ', Bar(), ' ', ETA()]).start()
    except:
        pbar = None
    i = 0

    for row in lst:
        fn(row)
        i = i + 1
        if pbar != None:
            try:
                pbar.update(i)
            except:
                pass

    if pbar != None:
        try:
            pbar.finish()
        except:
            pass


class Player(object):
    def __init__(self, total, alien, human):
        self.id = None
        self.total = total
        self.alien = alien
        self.human = human
    def rank(self, r):
        self.total.rank = r
        self.alien.rank = r
        self.human.rank = r

class Skill(object):
    def __init__(self, mu, sigma):
        self.skill = (mu, sigma)
        self.rank = None
