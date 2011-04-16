<html>
	<head>
		<title>Install ...</title>
		<style type='text/css'>
		</style>
	</head>

	<body>

<?php
	$extList = get_loaded_extensions();
	$phpVersion = explode(".", phpversion());
	
	
	function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	} # return_bytes()
?>

	<table>
		<tr> <th> PHP settings </th> <th> OK ? </th> </tr>
		<tr> <td> PHP version </td> <td> <?php echo ($phpVersion[0] >= '5' && $phpVersion[1] >= 3) ? "OK" : "PHP 5.3 or later is recommended" ?>  </td> </tr>
		<tr> <td> timezone settings </td> <td> <?php echo (ini_get("date.timezone")) ? "OK" : "Please specify date.timezone in your PHP.ini"; ?> </td> </tr>
		<tr> <td> Open base dir </td> <td> <?php echo (!ini_get("open_basedir")) ? "OK" : "Not empty, might be a problem"; ?>  </td> </tr>
		<tr> <td> PHP safe mode </td> <td> <?php echo ini_get('safe_mode') ? "Safe mode set -- will cause problems for retrieve.php" : "OK"; ?> </td> </tr>
		<tr> <td> Memory limit </td> <td> <?php echo return_bytes(ini_get('memory_limit')) < (32*1024*1024) ? "memory_limit below 32M" : "OK"; ?> </td> </tr>
	</table>
	
	<br>
	
	<table>
		<tr> <th> PHP extension </th> <th> OK ? </th> </tr>

		<tr> <td> SQLite </td> <td> <?php echo (array_search('SQLite', $extList) === false) ? "Not installed (geen probleem als MySQL geinstalleerd is)" : "OK" ?>  </td> </tr>
		<tr> <td> MySQL </td> <td> <?php echo (array_search('mysql', $extList) === false) ? "Not installed (geen probleem als sqlite3 geinstalleerd is)" : "OK" ?>  </td> </tr>
		<tr> <td> OpenSSL </td> <td> <?php echo (array_search('openssl', $extList) === false) ? "Not installed (geen probleem, maar het is wel aangeraden deze te installeren)" : "OK" ?>  </td> </tr>
		<tr> <td> bcmath </td> <td> <?php echo (array_search('bcmath', $extList) === false) ? "Not installed" : "OK" ?> </td> </tr>
		<tr> <td> ctype </td> <td> <?php echo (array_search('ctype', $extList) === false) ? "Not installed" : "OK" ?> </td> </tr>
		<tr> <td> xml </td> <td> <?php echo (array_search('xml', $extList) === false) ? "Not installed" : "OK" ?> </td> </tr>
		<tr> <td> zlib </td> <td> <?php echo (array_search('zlib', $extList) === false) ? "Not installed" : "OK" ?> </td> </tr>
	</table>

	<br>
	
<?php
	@include('settings.php');
	
	function ownWarning($errno, $errstr) {
		$GLOBALS['iserror'] = true;
		#echo $errstr;
	} # ownWarning

	function testInclude($fname) {
		$GLOBALS['iserror'] = false;
		include($fname);
		return !($GLOBALS['iserror']);
	} # testInclude
		
	set_error_handler("ownWarning",E_WARNING);
?>

	<table>
		<tr> <th> Include files  </th> <th> OK ? </th> </tr>
		<tr> <td> Settings file </td> <td> <?php echo testInclude("settings.php") ? "OK" : "settings.php cannot be read" ?>  </td> </tr>
		<tr> <td> PEAR </td> <td> <?php echo testInclude("System.php") ? "OK" : "PEAR cannot be found" ?> </td> </tr>
		<tr> <td> PEAR Net/NNTP </td> <td> <?php echo testInclude("Net/NNTP/Client.php") ? "OK" : "PEAR Net/NNTP package cannot be found" ?> </td> </tr>
		<tr> <td> NNTP server </td> <td> <?php echo (!empty($settings['nntp_nzb']['host']) === false) ? "No server entered" : "OK" ?>  </td> </tr>
	</table>
	
	<br> <br>
	
	<table>
		<tr> <th> Path </th> <th> PEAR found? </th> <th> Net/NNTP found? </th> <tr>
		
<?php
		$arInclude = explode(":", ini_get("include_path")); 
		for($i = 0; $i < count($arInclude); $i++) {
			echo "\t\t<tr><td>" . $arInclude[$i] . "</td> <td> " . 
						(file_exists($arInclude[$i] . 'System.php') ? "OK" : "") . "</td> <td>" .
						(file_exists($arInclude[$i] . "Net/NNTP/Client.php") ? "OK" : "") . " </td> </tr>";
		} # foreach
?>  
	</table>
<br>
<table>
<?php
	switch ($settings['nzbhandling']['action'] )
	{
		case "save":
		case "runcommand":
		case "push-sabnzbd":
			echo "<td><b>NZB local download enabled</b></td>";
			echo "<tr><td>NZB action: </td><td>" . $settings['nzbhandling']['action'] . "</td></tr>";
			echo "<tr><td>NZB directory: </td><td>" .$settings['nzbhandling']['local_dir'] ."</td></tr>";
			echo "<tr><td>Directory access: </td><td>";
			$TestFileName = $settings['nzbhandling']['local_dir'] ."testFile.txt";
			$TestFileHandle = fopen($TestFileName, 'w') or die("Cannot create file</td></tr>");
			echo "OK</td></tr>";
			fclose($TestFileHandle);
			unlink($TestFileName);
			break;
	}
?>
</table>
	
	</body>
</html>
