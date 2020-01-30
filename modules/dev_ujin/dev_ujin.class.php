<?php
/**
* Ujin devices 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 19:01:42 [Jan 05, 2020])
*/
//
//
class dev_ujin extends module {
/**
* dev_ujin
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="dev_ujin";
  $this->title="Ujin devices";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
	$this->getConfig();
	$out['DEBUG']=$this->config['DEBUG'];
	if(!$this->config['BUFF']) $this->config['BUFF']=1024;
	$out['BUFF']=$this->config['BUFF'];
	if ((time() - (int)gg('cycle_dev_ujinRun')) < 60*2 ) {
		$out['CYCLERUN'] = 1;
	} else {
		$out['CYCLERUN'] = 0;
	}
	if ($this->view_mode=='update_settings') {
		$this->config['DEBUG']=gr('debug');
		$this->config['BUFF']=gr('buff');
		$this->saveConfig();
		$this->redirect("?");
	}
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='dev_ujin_devices' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_dev_ujin_devices') {
   $this->search_dev_ujin_devices($out);
  }
  if ($this->view_mode=='edit_dev_ujin_devices') {
   $this->edit_dev_ujin_devices($out, $this->id);
  }
  if ($this->view_mode=='delete_dev_ujin_devices') {
   $this->delete_dev_ujin_devices($this->id);
   $this->redirect("?data_source=dev_ujin_devices");
  }
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='dev_ujin_data') {
  if ($this->view_mode=='' || $this->view_mode=='search_dev_ujin_data') {
   $this->search_dev_ujin_data($out);
  }
  if ($this->view_mode=='edit_dev_ujin_data') {
   $this->edit_dev_ujin_data($out, $this->id);
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* dev_ujin_devices search
*
* @access public
*/
 function search_dev_ujin_devices(&$out) {
  require(DIR_MODULES.$this->name.'/dev_ujin_devices_search.inc.php');
 }
/**
* dev_ujin_devices edit/add
*
* @access public
*/
 function edit_dev_ujin_devices(&$out, $id) {
  require(DIR_MODULES.$this->name.'/dev_ujin_devices_edit.inc.php');
 }
/**
* dev_ujin_devices delete record
*
* @access public
*/
 function delete_dev_ujin_devices($id) {
  $rec=SQLSelectOne("SELECT * FROM dev_ujin_devices WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM dev_ujin_devices WHERE ID='".$rec['ID']."'");
  SQLExec("DELETE FROM dev_ujin_data WHERE DEVICE_ID='".$rec['ID']."'");
 }
/**
* dev_ujin_data search
*
* @access public
*/
 function search_dev_ujin_data(&$out) {
  require(DIR_MODULES.$this->name.'/dev_ujin_data_search.inc.php');
 }
/**
* dev_ujin_data edit/add
*
* @access public
*/
 function edit_dev_ujin_data(&$out, $id) {
  require(DIR_MODULES.$this->name.'/dev_ujin_data_edit.inc.php');
 }
 function propertySetHandle($object, $property, $value) {
  $this->getConfig();
   $table='dev_ujin_data';
   $properties=SQLSelect("SELECT ID, DEVICE_ID, TITLE FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
        $device=SQLSelectOne("SELECT * FROM dev_ujin_devices WHERE ID=".$properties[$i]['DEVICE_ID']);
    	if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0)))
    	{
    		$errorcode = socket_last_error();
    		$errormsg = socket_strerror($errorcode);
				if($this->config['DEBUG']) debmes('[socket] '."Couldn't create socket: [$errorcode] $errormsg", 'dev_ujin_debug');
			die("Couldn't create socket: [$errorcode] $errormsg".PHP_EOL);
    	}
		if($this->config['DEBUG']) debmes('[socket] '."Socket created", 'dev_ujin_debug');
    	$buf_array['command']='management';
    	$buf_array['id']=intval($device['DEV_ID']);
        $buf_array['uniq_id']=intval(time() % 100 * 1000);
        $token=SQLSelectOne("SELECT VALUE FROM $table WHERE DEVICE_ID='".DBSafe($device['ID'])."' AND TITLE='token'");
        $buf_array['token']=$token['VALUE'];
        $metric_name=$properties[$i]['TITLE'];
        $buf_array[$metric_name]=$value;
        $buf=json_encode($buf_array);
		if($this->config['DEBUG']) debmes('[udp:'.$device['IP'].'] --- '.$buf, 'dev_ujin_debug');
		if( !socket_sendto($sock, $buf, strlen($buf) , 0 ,  $device['IP'], 30300))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
				if($this->config['DEBUG']) debmes('[socket] '."Could not send data: [$errorcode] $errormsg", 'dev_ujin_debug');
			die("Could not send data: [$errorcode] $errormsg \n");
		}
		if($this->config['DEBUG']) debmes('[socket] '."Message sended", 'dev_ujin_debug');
    }
   }
 }
 function processCycle($msg, $ip) {
    $this->getConfig();
      //to-do
	if($this->config['DEBUG']) debmes('[udp:'.$ip.'] +++ '.$msg, 'dev_ujin_debug');
    $msg_arr=json_decode($msg, TRUE);
    if(is_array($msg_arr['header'])) {
        $dev_id=$msg_arr['header']['id'];
        $device=SQLSelectOne("SELECT * FROM dev_ujin_devices WHERE DEV_ID = '".DBSafe($dev_id)."'");
        $total=count($device);
        if ($total) {
			if($ip != $device['IP'] || $msg_arr['header']['devName'] != $device['DEV_TYPE']) {
				$device['IP']=$ip;
				$device['DEV_TYPE']=$msg_arr['header']['devName'];
				sqlUpdate('dev_ujin_devices', $device);
			}
            if (is_array($msg_arr['header']['data'])) {
                $data=SQLSelect("SELECT * FROM dev_ujin_data WHERE DEVICE_ID = '".DBSafe($device['ID'])."'");
                foreach($msg_arr['header']['data'] as $k => $v) {
                    unset($find_m, $metric, $rec);
                    foreach($data as $metric) {
                        if ($k==$metric['TITLE']) {
                            $metric['VALUE']=$v;
                            if($metric['LINKED_OBJECT'] && $metric['LINKED_PROPERTY']) sg($metric['LINKED_OBJECT'].'.'.$metric['LINKED_PROPERTY'], $metric['VALUE'], array($this->name => '0'));
                            sqlUpdate('dev_ujin_data', $metric);
                            $find_m=true;
                            break;
                        }
                    }
                    if($find_m==true) continue;
                    $rec['TITLE']=$k;
                    $rec['VALUE']=$v;
                    $rec['DEVICE_ID']=$device['ID'];
                    sqlInsert('dev_ujin_data', $rec);
                }
            } else {
                echo "invalid data";
            }
        } else {
            $device['DEV_ID']=$msg_arr['header']['id'];
            $device['TITLE']=$msg_arr['header']['devName'];
            $device['DEV_TYPE']=$msg_arr['header']['devName'];
			$device['IP']=$ip;
            sqlInsert('dev_ujin_devices', $device);
        }
    } else {
        echo "invalid message";
    }
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS dev_ujin_devices');
  SQLExec('DROP TABLE IF EXISTS dev_ujin_data');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
dev_ujin_devices - 
dev_ujin_data - 
*/
  $data = <<<EOD
 dev_ujin_devices: ID int(10) unsigned NOT NULL auto_increment
 dev_ujin_devices: TITLE varchar(100) NOT NULL DEFAULT ''
 dev_ujin_devices: IP varchar(255) NOT NULL DEFAULT ''
 dev_ujin_devices: DEV_ID varchar(255) NOT NULL DEFAULT ''
 dev_ujin_devices: DEV_TYPE varchar(255) NOT NULL DEFAULT ''
 dev_ujin_devices: INFO varchar(255) NOT NULL DEFAULT ''
 dev_ujin_data: ID int(10) unsigned NOT NULL auto_increment
 dev_ujin_data: TITLE varchar(100) NOT NULL DEFAULT ''
 dev_ujin_data: VALUE varchar(255) NOT NULL DEFAULT ''
 dev_ujin_data: DEVICE_ID int(10) NOT NULL DEFAULT '0'
 dev_ujin_data: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 dev_ujin_data: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgSmFuIDA1LCAyMDIwIHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
