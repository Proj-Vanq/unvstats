# -*- coding: utf-8 -*-

import sys, os, re, zipfile
from PIL import Image
import StringIO
import tempfile

def dpkg_version_cmp(x, y):
	xp = 0
	yp = 0

	def order(c):
		if c.isdigit():
			return 0
		if c.isalpha():
			return ord(c)
		if c == '~':
			return -1
		return ord(c) + 256

	while xp < len(x) or yp < len(y):
		firstDiff = 0

		while (xp < len(x) and not x[xp].isdigit()) or (yp < len(y) and not y[yp].isdigit()):
			ac = order(x[xp]) if xp < len(x) else 0
			bc = order(y[yp]) if yp < len(y) else 0

			if ac != bc:
				return cmp(ac, bc)

			xp += 1
			yp += 1

		while xp < len(x) and x[xp] == '0':
			xp += 1
		while yp < len(y) and y[yp] == '0':
			yp += 1

		while (xp < len(x) and x[xp].isdigit()) and (yp < len(y) and y[yp].isdigit()):
			if firstDiff == 0:
				firstDiff = cmp((ord(x[xp]) if xp < len(x) else 0), (ord(y[yp]) if yp < len(y) else 0))
			xp += 1
			yp += 1

		if xp < len(x) and x[xp].isdigit():
			return 1
		if yp < len(y) and y[yp].isdigit():
			return -1
		if firstDiff:
			return firstDiff

	return 0

""" Class: Reader """
class Reader:
	""" Init Reader """
	def Main(self, dbc, Check_map_in_database, pk3_dir, one_pk3):
		# Regular expressions
		self.RE_FILESCAN = re.compile("^(levelshots|scripts)/(.+)\.(png|jpg|webp|tga|arena|crn)$")
		self.RE_ARENA    = re.compile("longname\s*\"(.*?)\"")

		# Localize parents function
		self.Check_map_in_database = Check_map_in_database

		# Internal data
		self.dbc     = dbc
		self.pk3_dir = pk3_dir

		# one pk3
		if one_pk3 != None:
			if os.path.isfile(one_pk3) and one_pk3.endswith('.pk3'):
				print "Reading " + one_pk3 + "..."
				self.Scan_PK3(one_pk3)
			else:
				print "file not found, or not a .pk3"
			return;
	
		# Check the directory	
		if not os.path.isdir(self.pk3_dir):
			sys.exit("PK3 directory does not exist")

		pk3s = [i for i in os.listdir(self.pk3_dir) if re.match('^map-[^_]+_.*\.pk3$', i)]
		pk3s.sort(cmp=lambda x,y: dpkg_version_cmp(x[:-4], y[:-4]))

		# Loop through all files
		for singlefile in pk3s:
			# Check if file is ok
			if os.path.isfile(self.pk3_dir + '/' + singlefile) and re.match('^map-[^_]+_.*\.pk3$', singlefile):
				print "Reading " + singlefile + " ..."
				filepath = self.pk3_dir + '/' + singlefile
				self.Scan_PK3(filepath)

	""" Scan a single PK3 file """
	def Scan_PK3(self, filename):
		try:
			pk3 = zipfile.ZipFile(filename, 'r')
			namelist = pk3.namelist()
		except:
			print "Error while reading " + filename
			return

		for pk3file in namelist:
			# Check the file
			match = self.RE_FILESCAN.search(pk3file)
			if match == None:
				continue

			# The file is something we want, save it
			result    = match.groups()
			filetype  = result[0]
			mapname   = result[1]
			extension = result[2]

			if filetype == 'levelshots' and extension != 'arena':
				self.Save_levelshot(pk3, mapname, extension)
			elif filetype == 'scripts' and extension == 'arena':
				self.Save_mapname(pk3, mapname)

	""" Save a levelshot """
	def Save_levelshot(self, pk3, mapname, extension):
		data = pk3.read('levelshots/' + mapname + '.' + extension)
		srcimg = None
		dstimg = None
		tmpname = None

		try:
			if extension == 'webp':
				# We need to convert to PNG: use dwebp
				# This is expected to break on Windows (or maybe any non-POSIX)
				srcimg = tempfile.NamedTemporaryFile(suffix = '.webp')
				dstimg = tempfile.NamedTemporaryFile(suffix = '.png')

				srcimg.file.write(data)
				srcimg.file.flush()

				print 'running dwebp %s -o %s' % (srcimg.name, dstimg.name)
				ret = os.spawnlp(os.P_WAIT, 'dwebp', 'dwebp', srcimg.name, '-o', dstimg.name)
				if ret:
					raise Exception('dwebp returned %d' % ret)

				dstimg.file.seek(0)
				data = dstimg.file.read()
				# now we have PNG
			elif extension == 'crn':
				# We need to convert to PNG: use crunch
				# This is expected to break on Windows (or maybe any non-POSIX)
				# crunch actually replaces the output file, so work around that
				srcimg = tempfile.NamedTemporaryFile(suffix = '.crn')
				tmpname = srcimg.name[:-3] + 'png'
				srcimg.file.write(data)
				srcimg.file.flush()

				print 'running crunch -fileformat png -outsamedir %s' % (srcimg.name)
				ret = os.spawnlp(os.P_WAIT, 'crunch', 'crunch', '-fileformat', 'png', '-outsamedir', srcimg.name)
				if ret:
					raise Exception('crunch returned %d' % ret)

				with open(tmpname, 'rb') as tmpimg:
					data = tmpimg.read()
				# now we have PNG

			image = Image.open(StringIO.StringIO(data))
			image.thumbnail((256, 144), Image.BICUBIC)
			levelshot = StringIO.StringIO()
			image.save(levelshot, 'JPEG')
			levelshot_string = levelshot.getvalue()
		except Exception as e:
			print "Error while processing levelshot %s.%s: %s" % (mapname, extension, e)
			if srcimg != None:
				srcimg.file.close()
			if dstimg != None:
				dstimg.file.close()
			if tmpname != None:
				os.unlink(tmpname)
			return

		if srcimg != None:
			srcimg.file.close()
		if dstimg != None:
			dstimg.file.close()
		if tmpname != None:
			os.unlink(tmpname)

		# Image thumbnail created, insert it into database
		map_id    = self.Check_map_in_database(mapname)

		self.dbc.execute("UPDATE `maps` SET `map_levelshot` = %s WHERE map_id = %s", (levelshot_string, map_id))


	""" Save a mapname """
	def Save_mapname(self, pk3, mapname):
		data = pk3.read('scripts/' + mapname + '.arena')

		# Check the data
		match = self.RE_ARENA.search(data)
		if match == None:
			return

		# The data contains a longname, put it into the database
		result    = match.groups()
		longname  = result[0]
		map_id    = self.Check_map_in_database(mapname)

		self.dbc.execute("UPDATE `maps` SET `map_longname` = %s WHERE map_id = %s", (longname, map_id))
		
