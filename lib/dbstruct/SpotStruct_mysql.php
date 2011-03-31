<?php
require_once "lib/dbstruct/SpotStruct_abs.php";

class SpotStruct_mysql extends SpotStruct_abs {

	function createDatabase() {
		$q = $this->_dbcon->arrayQuery("SHOW TABLES");
		if (empty($q)) {
			$this->_dbcon->rawExec("CREATE TABLE spots(id INTEGER PRIMARY KEY AUTO_INCREMENT, 
										messageid varchar(128),
										spotid INTEGER,
										category INTEGER, 
										subcat INTEGER,
										poster VARCHAR(128),
										groupname VARCHAR(128),
										subcata VARCHAR(64),
										subcatb VARCHAR(64),
										subcatc VARCHAR(64),
										subcatd VARCHAR(64),
										title VARCHAR(128),
										tag VARCHAR(128),
										stamp INTEGER,
										filesize BIGINT DEFAULT 0,
										moderated BOOLEAN DEFAULT FALSE) ENGINE = MYISAM;");
			$this->_dbcon->rawExec("CREATE INDEX idx_spots_1 ON spots(id, category, subcata, subcatd, stamp DESC)");
			$this->_dbcon->rawExec("CREATE INDEX idx_spots_2 ON spots(id, category, subcatd, stamp DESC)");
			$this->_dbcon->rawExec("CREATE UNIQUE INDEX idx_spots_3 ON spots(messageid)");
			$this->_dbcon->rawExec("CREATE INDEX idx_spots_4 ON spots(stamp);");
			$this->_dbcon->rawExec("CREATE INDEX idx_spots_5 ON spots(poster);");

			$this->_dbcon->rawExec("CREATE FULLTEXT INDEX idx_spots_fts_1 ON spots(title);");
			$this->_dbcon->rawExec("CREATE FULLTEXT INDEX idx_spots_fts_2 ON spots(poster);");
			$this->_dbcon->rawExec("CREATE FULLTEXT INDEX idx_spots_fts_3 ON spots(tag);");

			# spotsfull
			$this->_dbcon->rawExec("CREATE TABLE spotsfull(id INTEGER PRIMARY KEY AUTO_INCREMENT, 
										messageid varchar(128),
										userid varchar(32),
										verified BOOLEAN,
										usersignature TEXT,
										userkey TEXT,
										xmlsignature TEXT,
										fullxml TEXT,
										filesize BIGINT) ENGINE = MYISAM;");										

			# create indices
			$this->_dbcon->rawExec("CREATE UNIQUE INDEX idx_spotsfull_1 ON spotsfull(messageid, userid)");
			$this->_dbcon->rawExec("CREATE INDEX idx_spotsfull_2 ON spotsfull(userid);");
			
			# NNTP table
			$this->_dbcon->rawExec("CREATE TABLE nntp(server varchar(128) PRIMARY KEY,
										   maxarticleid INTEGER UNIQUE,
										   nowrunning INTEGER DEFAULT 0,
										   lastrun INTEGER DEFAULT 0);");

			# commentsxover
			$this->_dbcon->rawExec("CREATE TABLE commentsxover(id INTEGER PRIMARY KEY AUTO_INCREMENT,
										   messageid VARCHAR(128),
										   nntpref VARCHAR(128));");
			$this->_dbcon->rawExec("CREATE INDEX idx_commentsxover_1 ON commentsxover(nntpref, messageid)");
			$this->_dbcon->rawExec("CREATE UNIQUE INDEX idx_commentsxover_2 ON commentsxover(messageid)");
			
			# downloadlist
			$this->_dbcon->rawExec("CREATE TABLE downloadlist(id INTEGER PRIMARY KEY AUTO_INCREMENT,
										   messageid VARCHAR(128),
										   stamp INTEGER);");
			$this->_dbcon->rawExec("CREATE INDEX idx_downloadlist_1 ON downloadlist(messageid)");

			# watchlist
			$this->_dbcon->rawExec("CREATE TABLE watchlist(id INTEGER PRIMARY KEY AUTO_INCREMENT, 
												   messageid VARCHAR(128),
												   dateadded INTEGER,
												   comment TEXT) ENGINE = MYISAM;");
			$this->_dbcon->rawExec("CREATE UNIQUE INDEX idx_watchlist_1 ON watchlist(messageid)");
		} # if
	} # createDatabase

	function updateSchema() {
		# Controleer of er wel een fulltext index zit op 'spots' tabel 
		$q = $this->_dbcon->arrayQuery("SHOW INDEXES FROM spots WHERE key_name = 'idx_spots_fts_1'");
		if (empty($q)) {
			$this->_dbcon->rawExec("CREATE FULLTEXT INDEX idx_spots_fts_1 ON spots(title);");
			$this->_dbcon->rawExec("CREATE FULLTEXT INDEX idx_spots_fts_2 ON spots(poster);");
			$this->_dbcon->rawExec("CREATE FULLTEXT INDEX idx_spots_fts_3 ON spots(tag);");
		} # if

		# Controleer of er wel een fulltext index zit op 'spotsfull' tabel 
		$q = $this->_dbcon->arrayQuery("SHOW INDEXES FROM spotsfull WHERE key_name = 'idx_spotsfull_fts_1'");
		if (empty($q)) {
			$this->_dbcon->rawExec("CREATE FULLTEXT INDEX idx_spotsfull_fts_1 ON spotsfull(userid);");
		} # if 
	} # updateSchema
	
} # class
