<?php
error_reporting(E_ALL & ~8192 & ~E_USER_WARNING);	# 8192 == E_DEPRECATED maar PHP < 5.3 heeft die niet
session_start();

require_once "lib/SpotClassAutoload.php";
SpotTiming::start('total');
SpotTiming::start('includes');
require_once "lib/SpotCookie.php";
require_once "settings.php";
SpotTiming::stop('includes');

#- main() -#
try {
	# database object
	$db = new SpotDb($settings['db']);
	$db->connect();

	# Creer het settings object
	$settings = SpotSettings::singleton($db, $settings);

	# enable of disable de timer
	if (!$settings->get('enable_timing')) {
		SpotTiming::disable();
	} # if
	
	# Controleer eerst of het schema nog wel geldig is
	if (!$db->schemaValid()) {
		die("Database schema is gewijzigd, draai upgrade-db.php aub" . PHP_EOL);
	} # if
	
	# Controleer dat er wel een password salt ingevuld is
	if ($settings->get('pass_salt') == 'unieke string') {
		die("Verander de setting 'pass_salt' in je ownsettings.php naar iets unieks!" . PHP_EOL);
	} # if
	
	# Haal het userobject op dat 'ingelogged' is
	SpotTiming::start('auth');
	$spotUserSystem = new SpotUserSystem($db, $settings);
	$userSession = $spotUserSystem->useOrStartSession();
	$currentUser = $userSession['user'];
	SpotTiming::stop('auth');

	# helper functions for passed variables
	$req = new SpotReq();
	$req->initialize();
	$page = $req->getDef('page', 'index');
		
	SpotTiming::start('renderpage');
	switch($page) {
		case 'render' : {
				$page = new SpotPage_render($db, $settings, $currentUser, $req->getDef('tplname', ''),
							Array('search' => $req->getDef('search', $settings->get('index_filter')),
								  'messageid' => $req->getDef('messageid', ''),
								  'pagenr' => $req->getDef('pagenr', 0),
								  'sortby' => $req->getDef('sortby', ''),
								  'sortdir' => $req->getDef('sortdir', '')));

				$page->render();
				break;
		} # render
		
		case 'getspot' : {
				$page = new SpotPage_getspot($db, $settings, $currentUser, $req->getDef('messageid', ''));
				$page->render();
				break;
		} # getspot

		case 'getnzb' : {
				$page = new SpotPage_getnzb($db, $settings, $currentUser, 
								Array('messageid' => $req->getDef('messageid', ''),
									  'action' => $req->getDef('action', 'display')));
				$page->render();
				break;
		}
		
		case 'getspotmobile' : {
				$page = new SpotPage_getspotmobile($db, $settings, $currentUser, $req->getDef('messageid', ''));
				$page->render();
				break;
		} # getspotmobile

		case 'getnzbmobile' : {
				$page = new SpotPage_getnzbmobile($db, $settings, $currentUser,
								Array('messageid' => $req->getDef('messageid', ''),
									  'action' => $req->getDef('action', 'display')));
				$page->render();
				break;
		} # getnzbmobile		

		case 'erasedls' : {
				$page = new SpotPage_erasedls($db, $settings, $currentUser);
				$page->render();
				break;
		} # erasedls
		
		case 'catsjson' : {
				$page = new SpotPage_catsjson($db, $settings, $currentUser);
				$page->render();
				break;
		} # getspot
		
		case 'markallasread' : {
				$page = new SpotPage_markallasread($db, $settings, $currentUser);
				$page->render();
				break;
		} # markallasread

		case 'getimage' : {
			$page = new SpotPage_getimage($db, $settings, $currentUser,
								Array('messageid' => $req->getDef('messageid', ''),
									  'image' => $req->getDef('image', Array())));
			$page->render();
			break;
		}

		case 'selecttemplate' : {
				$page = new SpotPage_selecttemplate($db, $settings, $currentUser, $req);
				$page->render();
				break;
		} # selecttemplate

		case 'atom' : {
			$page = new SpotPage_atom($db, $settings, $currentUser,
					Array('search' => $req->getDef('search', $settings->get('index_filter')),
						  'page' => $req->getDef('page', 0),
						  'sortby' => $req->getDef('sortby', ''),
						  'sortdir' => $req->getDef('sortdir', ''))
			);
			$page->render();
			break;
		} # atom
		
		case 'statics' : {
				$page = new SpotPage_statics($db, $settings, $currentUser,
							Array('type' => $req->getDef('type', '')));
				$page->render();
				break;
		} # statics

		default : {
				$page = new SpotPage_index($db, $settings, $currentUser,
							Array('search' => $req->getDef('search', $settings->get('index_filter')),
								  'pagenr' => $req->getDef('pagenr', 0),
								  'sortby' => $req->getDef('sortby', ''),
								  'sortdir' => $req->getDef('sortdir', ''),
								  'messageid' => $req->getDef('messageid', ''),
								  'action' => $req->getDef('action', ''))
					);
				$page->render();
				break;
		} # default
	} # switch
	SpotTiming::stop('renderpage');

	# timing
	SpotTiming::stop('total');

	# enable of disable de timer
	if (($settings->get('enable_timing')) && (!in_array(SpotReq::getDef('page', ''), array('catsjson', 'getnzb')))) {
		SpotTiming::display();
	} # if
	
}
catch(Exception $x) {
	var_dump($x);
	die($x->getMessage());
} # catch
