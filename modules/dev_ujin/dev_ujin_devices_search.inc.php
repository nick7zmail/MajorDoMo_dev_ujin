<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['dev_ujin_devices_qry'];
  } else {
   $session->data['dev_ujin_devices_qry']=$qry;
  }
  if (!$qry) $qry="1";
  $sortby_dev_ujin_devices="ID DESC";
  $out['SORTBY']=$sortby_dev_ujin_devices;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM dev_ujin_devices WHERE $qry ORDER BY ".$sortby_dev_ujin_devices);
  if ($res[0]['ID']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
	   if(stripos($res[$i]['DEV_TYPE'], 'msensor')!== false) $res[$i]['IMG']='msensor';
	   elseif(stripos($res[$i]['DEV_TYPE'], 'dinRelay')!== false) $res[$i]['IMG']='dinRelay';
	   elseif(stripos($res[$i]['DEV_TYPE'], 'sdimmer')!== false) $res[$i]['IMG']='sdimmer';
	   elseif(stripos($res[$i]['DEV_TYPE'], 'termostat')!== false) $res[$i]['IMG']='termostat';
	   elseif(stripos($res[$i]['DEV_TYPE'], 'sonoff_ext_socket')!== false) $res[$i]['IMG']='sonoff_ext_socket';
	   else $res[$i]['IMG']='unknown';
    // some action for every record if required
   }
   $out['RESULT']=$res;
  }
