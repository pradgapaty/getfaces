<?php

require_once("/var/www/html/getfaces/Helpers/LoaderHelper.php");

//Statuses:
//0 - photo without status
//1 - photo needed analyze
//2 - photo after analyze
//3 - photo will be deleted

if (!empty($argv[1])) {
	$action = $argv[1];
} else {
	$action = "";
}
$runner = new Runner();

switch ($action) {
	case "getPhoto":
		$runner->doGetPhoto();		
		break;
	case "analyzePhoto":
		$runner->doAnalyzePhoto();
		break;
	case "syncLocalData":
		$runner->doSyncLocalData();
		break;
	default:
		$log = new LogHelper();
		$log->displayDebug("Error", "Incorrect argument");
		break;
}

class Runner
{
	private $parsingLimit = 50;
	private $parsingDelay = 10;
	private $enableParsing = false;

	private $logClassInst;

	public function __construct()
	{
		$this->logClassInst = new LogHelper();
	}
    /**
     * Run get photo method
     *
     *
     * @return void
     */
	public function doGetPhoto()
	{
		$getPhotoClssInst = new GetPhoto();
		$logClassInst = new LogHelper();

		if ($this->enableParsing) {
			for ($i = 0; $i < $this->parsingLimit; $i++) {
				$getPhotoClssInst->getPhoto();
				sleep($this->parsingDelay);
				print_r("[" . $i . "] \n");
			}
		} else {
			$logClassInst->displayDebug("Info", "Parsing disable in the settings");
		}
	}

    /**
     * Run analyze photo method
     *
     *
     * @return void
     */
	public function doAnalyzePhoto()
	{
		$analizePhoto = new AnalyzePhoto();
		$analizePhoto->photoAnalyze();
	}

    /**
     * Run sync local datamethod
     *
     *
     * @return void
     */
	public function doSyncLocalData()
	{
		$sync = new SyncLocalData();
		$sync->syncFilesDb();
		$sync->syncDbFiles();
	}
}

