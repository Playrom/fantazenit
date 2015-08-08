<?php

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}

require_once('config.php');

ini_set('auto_detect_line_endings', TRUE); 
	
$database = new ConnectDatabase(DATABASE_HOST,DATABASE_USERNAME,DATABASE_PASSWORD,DATABASE_NAME,DATABASE_PORT);
$database_files = new ConnectDatabaseFiles($database->mysqli);

$config=$database->dumpConfig();



/*** CARICAMENTO QUOTAZIONI ***/



for($round=1;$round<39;$round++){

	$url = 'http://www.fantagazzetta.com/download.aspx?key=quotazioni&g='.$round;
	$datastring="__EVENTTARGET=ctl00%24ContentPlaceHolderElle%24Download1%24LinkButtonExcelM&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTE4OTgyODA2OTAPZBYCZg9kFgICBw9kFgQCBA9kFgQCBw9kFgICAQ8WAh4HVmlzaWJsZWhkAgkPZBYEAgEPFgIfAGgWBGYPD2QWAh4Jb25rZXlkb3duBagBaWYoZXZlbnQud2hpY2ggfHwgZXZlbnQua2V5Q29kZSl7aWYgKChldmVudC53aGljaCA9PSAxMykgfHwgKGV2ZW50LmtleUNvZGUgPT0gMTMpKSB7ZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ0J1dHRvblN1Ym1pdCcpLmNsaWNrKCk7cmV0dXJuIGZhbHNlO319IGVsc2Uge3JldHVybiB0cnVlfTsgZAIBDw9kFgIfAQWoAWlmKGV2ZW50LndoaWNoIHx8IGV2ZW50LmtleUNvZGUpe2lmICgoZXZlbnQud2hpY2ggPT0gMTMpIHx8IChldmVudC5rZXlDb2RlID09IDEzKSkge2RvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdCdXR0b25TdWJtaXQnKS5jbGljaygpO3JldHVybiBmYWxzZTt9fSBlbHNlIHtyZXR1cm4gdHJ1ZX07IGQCAw9kFgICAQ8WAh4EVGV4dAUHcGxheXJvbWQCDg9kFgICAQ9kFgQCAQ8WAh8AaGQCAw9kFgICAQ9kFgICAw9kFgICAQ8WAh8AaGQYAQVKY3RsMDAkSGVhZGVyTWFzdGVyUGFnZUVsbGUkUmVnaXN0cmF6aW9uZTEkT3BlbkF1dGhQcm92aWRlcnMxJHByb3ZpZGVyc0xpc3QPFCsADmRkZGRkZGQUKwABZAIBZGRkZgL%2F%2F%2F%2F%2FD2S%2FaHqa1%2BkHIJz5lUoCM%2F%2FNs1GaCA%3D%3D&__VIEWSTATEGENERATOR=E0C68A58&__EVENTVALIDATION=%2FwEdAAYEw73CM6XqnfGnG60HIsPPFBXkjck%2F0%2FWLvIivqq6sTrjQFmWsAGgruuJ%2FUEtKMm80EE091rYqt35i3d%2FGRKIfvojBl4A4myFl3Oj9V0BahfoBDolif1TAI%2FNh9txN4CXTxLwP%2BV7fbIv2j9Y9E%2Boh8AEu5w%3D%3D";


	libxml_use_internal_errors(true);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl,CURLOPT_ENCODING , "");
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_POSTFIELDS,$datastring);
	$data = curl_exec($curl);
	curl_close($curl);


	$path=PATH_UPLOADS."/quotazioni-".$round.".xml";
	
	$myfile = fopen($path, "w+") or die("Unable to open file!");
	fwrite($myfile, $data);

	$ret=$database_files->loadPlayersToDatabase($path,$round);

	fclose($myfile);

	if(!$ret) unlink($path);
	


	/*** CARICAMENTO VOTI ***/
	

	
	$url = 'http://www.fantagazzetta.com/download.aspx?key=voti&g='.$round;
	$datastring="__EVENTTARGET=ctl00%24ContentPlaceHolderElle%24Download1%24LinkButtonExcelM&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTE4OTgyODA2OTAPZBYCZg9kFgICBw9kFgQCBA9kFgQCBw9kFgICAQ8WAh4HVmlzaWJsZWhkAgkPZBYEAgEPFgIfAGgWBGYPD2QWAh4Jb25rZXlkb3duBagBaWYoZXZlbnQud2hpY2ggfHwgZXZlbnQua2V5Q29kZSl7aWYgKChldmVudC53aGljaCA9PSAxMykgfHwgKGV2ZW50LmtleUNvZGUgPT0gMTMpKSB7ZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ0J1dHRvblN1Ym1pdCcpLmNsaWNrKCk7cmV0dXJuIGZhbHNlO319IGVsc2Uge3JldHVybiB0cnVlfTsgZAIBDw9kFgIfAQWoAWlmKGV2ZW50LndoaWNoIHx8IGV2ZW50LmtleUNvZGUpe2lmICgoZXZlbnQud2hpY2ggPT0gMTMpIHx8IChldmVudC5rZXlDb2RlID09IDEzKSkge2RvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdCdXR0b25TdWJtaXQnKS5jbGljaygpO3JldHVybiBmYWxzZTt9fSBlbHNlIHtyZXR1cm4gdHJ1ZX07IGQCAw9kFgICAQ8WAh4EVGV4dAUHcGxheXJvbWQCDg9kFgICAQ9kFgQCAQ8WAh8AaGQCAw9kFgICAQ9kFgICAw9kFgICAQ8WAh8AaGQYAQVKY3RsMDAkSGVhZGVyTWFzdGVyUGFnZUVsbGUkUmVnaXN0cmF6aW9uZTEkT3BlbkF1dGhQcm92aWRlcnMxJHByb3ZpZGVyc0xpc3QPFCsADmRkZGRkZGQUKwABZAIBZGRkZgL%2F%2F%2F%2F%2FD2S%2FaHqa1%2BkHIJz5lUoCM%2F%2FNs1GaCA%3D%3D&__VIEWSTATEGENERATOR=E0C68A58&__EVENTVALIDATION=%2FwEdAAYEw73CM6XqnfGnG60HIsPPFBXkjck%2F0%2FWLvIivqq6sTrjQFmWsAGgruuJ%2FUEtKMm80EE091rYqt35i3d%2FGRKIfvojBl4A4myFl3Oj9V0BahfoBDolif1TAI%2FNh9txN4CXTxLwP%2BV7fbIv2j9Y9E%2Boh8AEu5w%3D%3D";

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl,CURLOPT_ENCODING , "");
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_POSTFIELDS,$datastring);
	$data = curl_exec($curl);
	curl_close($curl);


	$path=PATH_UPLOADS."/voti-".$round.".xml";

	$myfile = fopen($path, "w+") or die("Unable to open file!");
	fwrite($myfile, $data);

	$database_files->loadStatsToDatabase($round,$path);

	fclose($myfile);

	if(!$ret) unlink($path);
	


}




?>