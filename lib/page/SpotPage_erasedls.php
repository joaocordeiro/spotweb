<?php
require_once "lib/page/SpotPage_Abs.php";

class SpotPage_erasedls extends SpotPage_Abs {
	
	function render() {
		$this->_db->emptyDownloadList($this->_currentUser['userid']);
		
		echo "<xml><return>ok</return></xml>";
	} # render()

} # SpotPage_erasedls
