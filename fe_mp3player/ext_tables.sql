#
# Table structure for table 'tx_femp3player_playlists'
#
CREATE TABLE tx_femp3player_playlists (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	playlist longblob NOT NULL,
	dir_path text NOT NULL,
	dir_songs blob NOT NULL,
	dir_titles text NOT NULL,
	podcast_url text NOT NULL,
	nbo_podcast blob NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
