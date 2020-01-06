<?php
chdir(dirname(__FILE__) . '/../');
include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");
set_time_limit(0);
// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
$ctl = new control_modules();

include_once(DIR_MODULES . 'dev_ujin/dev_ujin.class.php');
$dev_ujin_module = new dev_ujin();
$dev_ujin_module->getConfig();

echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;
$latest_check=0;
$checkEvery=10; // poll every 5 seconds

	 
	//Create a UDP socket
	if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0)))
	{
		$errorcode = socket_last_error();
		$errormsg = socket_strerror($errorcode);
		 
		die("Couldn't create socket: [$errorcode] $errormsg".PHP_EOL);
	}
	 
	echo "Socket created".PHP_EOL;
	 
	 
	// привязка исходного адреса
	if( !socket_bind($sock, "0.0.0.0" , 30300) )
	{
		$errorcode = socket_last_error();
		$errormsg = socket_strerror($errorcode);
		 
		die("Could not bind socket : [$errorcode] $errormsg".PHP_EOL);
	}
	 
	echo "Socket bind OK".PHP_EOL;


while (1)
{
   setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
    if ((time()-$latest_check)>$checkEvery) {
        $latest_check=time();
        echo date('Y-m-d H:i:s').' Polling devices...';
    }

		$r = socket_recvfrom($sock, $buf, 512, 0, $remote_ip, $remote_port);
		echo "$remote_ip : $remote_port -- " . $buf .PHP_EOL;
		 $dev_ujin_module->processCycle($buf);
		

   if (file_exists('./reboot') || IsSet($_GET['onetime']))
   {
      socket_close($sock);
      $db->Disconnect();
      exit;
   }
}
DebMes("Unexpected close of cycle: " . basename(__FILE__));


