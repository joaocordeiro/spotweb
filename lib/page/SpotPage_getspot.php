<?php
require_once "lib/page/SpotPage_Abs.php";
require_once "lib/SpotCategories.php";

class SpotPage_getspot extends SpotPage_Abs {
	private $_messageid;
	
	function __construct($db, $settings, $prefs, $messageid) {
		parent::__construct($db, $settings, $prefs);
		$this->_messageid = $messageid;
	} # ctor


	function render() {
		$spotnntp = new SpotNntp($this->_settings['nntp_hdr'], $this->_settings['use_openssl']);
		
		# Haal de volledige spotinhoud op
		$spotsOverview = new SpotsOverview($this->_db, $this->_settings);
		$fullSpot = $spotsOverview->getFullSpot($this->_messageid, $spotnntp);

		# zet de page title
		$this->_pageTitle = "spot: " . $fullSpot['title'];
		
		#- display stuff -#
		$this->template('header');
		$this->template('spotinfo', array('spot' => $fullSpot));
		$this->template('footer');
	} # render
	
} # class SpotPage_getspot
