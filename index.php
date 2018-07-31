<?php
/*
--  8""""8 8""""8 8     8""""8 8"""88    8""""8 8""""8   
--  8    " 8    8 8     8    8 8    8    8    8 8    8   
--  8e     8eeee8 8e    8eeee8 8    8    8e   8 8eeee8ee 
--  88     88   8 88    88   8 8    8    88   8 88     8 
--  88   e 88   8 88    88   8 8    8    88   8 88     8 
--  88eee8 88   8 88eee 88   8 8eeee8    88eee8 88eeeee8 
--                                                       
*/

/**
* Class DB_
* La classe qui gêre les accès aux données
* @package  TrogonCalao
* @author   Florian Hourdin <florian.hourdin59300@gmail.com>
*/

Class DB_ {
    private $CONFIG;
    private $startTime;
    private $endTime;
    private $countRequests;

    public function __construct($SETTINGS = array('DIR' => 'DATA', 'DEBUG' => True, 'TRANSACTION_HISTORY' => True)) {
        $this->CONFIG = $SETTINGS;
        $this->startTime = microtime(true);
        $this->countRequests = 0;
    }

    public function getPlaceFor($sets = array('key' => '', 'value' => '', 'return' => 'all', 'cache' => True)) {
        $key = strtolower($sets['key']);
        $value = strtolower($sets['value']);
        $return = $sets['return'];
        $cache = $sets['cache'];

        $value_ = str_split($value, 2);
		$save = '';
		foreach ($value_ as $key_ => $value_) {
			$save .= "/".$value_;
		}

        if($cache != True) {
            $temp_db = new SQLite3('./'.$this->CONFIG['DIR'].'/LIST_ENTRIES.db');
            $temp_db->busyTimeout(10000);
            $temp_db_block = $temp_db->query('SELECT `PATH` FROM `ENTRIES_BY_KEY_VALUE` WHERE `KEY` = "'.$key.'" AND `VALUE` = "'.$value.'"')->fetchArray()['PATH'];
        }
        else{
            $value_ = str_split($value, 2);
            $save = '';
            foreach ($value_ as $key_ => $value_) {
                $save .= "/".$value_;
                if(!is_dir('./'.$this->CONFIG['DIR'].'/CACHE/nodes'.$save.'/')){
                    mkdir('./'.$this->CONFIG['DIR'].'/CACHE/nodes'.$save.'/', 0777);
                }
            }
            if(file_exists('./'.$this->CONFIG['DIR'].'/CACHE/node'.$save.'/node')) {
                $get_contents_node = file_get_contents('./'.$this->CONFIG['DIR'].'/CACHE/nodes'.$save.'/node');
                if(!empty($get_contents_node)) {
                    return $get_contents_node;   
                }
                else{
                    $error_get_contents_node = True;
                }
            }
            else{
                $error_get_contents_node = True;
            }
            if(isset($error_get_contents_node)){
                $temp_db = new SQLite3('./'.$this->CONFIG['DIR'].'/LIST_ENTRIES.db');
                $temp_db->busyTimeout(10000);
                $temp_db_block = $temp_db->query('SELECT `PATH` FROM `ENTRIES_BY_KEY_VALUE` WHERE `KEY` = "'.$key.'" AND `VALUE` = "'.$value.'"')->fetchArray()['PATH'];
                $fp = fopen('./'.$this->CONFIG['DIR'].'/CACHE/nodes'.$save.'/node', "a+");
                fputs($fp, $temp_db_block);
                fclose($fp);
            }
        }
        
        $this->countRequests++;
        if($return == 'node') {
            // return the node id
            return $temp_db_block;
        }
        elseif($return == 'path') {
            // return the path inside the node
            return $save;
        }
        else{
            // return the complete path with the node
            return $temp_db_block.$save;
        }

        unset($temp_db);
        unset($temp_db_block);
    }

    public function newSpawn($sets = array('key' => '', 'value' => '')) {
        $key = $sets['key'];
        $value = $sets['value'];
        $temp_db = new SQLite3('./'.$this->CONFIG['DIR'].'/LIST_ENTRIES.db');
        $temp_db->busyTimeout(10000);
        if(empty($temp_db->query('SELECT `PATH` FROM ENTRIES_BY_KEY_VALUE WHERE `KEY` = "'.$key.'" AND `VALUE` = "'.$value.'" ORDER BY ID ASC LIMIT 1')->fetchArray()['PATH'])) {
            $regdb_db_blocks = new SQLite3("./".$this->CONFIG['DIR']."/NODES.db");
            $regdb_db_blocks->busyTimeout(10000);
            $db_block = $regdb_db_blocks->query('SELECT NAME as best FROM LIST_NODES_BY_NAME ORDER BY COUNT_ENTRIES ASC LIMIT 1')->fetchArray()['best'];

            $value_ = str_split($value, 2);
            $save = '';
            $context = "/".$db_block;

            foreach ($value_ as $key_ => $value_) {
                $save .= "/".$value_;
                if(!is_dir("./".$this->CONFIG['DIR'].$context.$save."/")){
                    mkdir("./".$this->CONFIG['DIR'].$context.$save."/", 0777);
                }
            }
            if(!file_exists("./".$this->CONFIG['DIR'].$context.$save."/.spawn")) {
                $regdb_db_blocks->query('UPDATE LIST_NODES_BY_NAME SET COUNT_ENTRIES = COUNT_ENTRIES + 1 WHERE `NAME` = "'.$db_block.'"');
                $temp_db = new SQLite3('./'.$this->CONFIG['DIR'].'/LIST_ENTRIES.db');
                $temp_db->busyTimeout(10000);
                $temp_db_block = $temp_db->query('INSERT INTO ENTRIES_BY_KEY_VALUE(`PATH`,`KEY`,`VALUE`) VALUES("'.$db_block.'","'.$key.'","'.$value.'")');        
                $fp = fopen("./".$this->CONFIG['DIR'].$context.$save."/.spawn", "a+");
                if(fputs($fp, "created=".time())) {
                    $ok = True;
                }
                fclose($fp);
            }
            $this->countRequests++;
        }
        if(isset($ok)) {
            return True;
        }
    }

    public function getDataFor($sets = array('key' => '', 'value' => '', 'data' => '')) {
        $key = $sets['key'];
        $value = $sets['value'];
        $data = $sets['data'];

		$value_ = str_split($value, 2);
		$save = '';
		foreach ($value_ as $key_ => $value_) {
			$save .= "/".$value_;
        }

        $context = "./".$this->CONFIG['DIR']."/";
        $context .= $this->getPlaceFor(array('key' => $key, 'value' => $value, 'return' => 'node', 'cache' => True));
        $context .= "/".$save."/".$data;
        $this->countRequests++;
		if(file_exists($context)){
			$fp = file_get_contents($context, "r");
			return $fp;
		}
		else{
			return False;
		}
    }
    
    public function setDataFor($sets = array('key' => '', 'value' => '', 'data_key' => '', 'data_value' => '')) {
		$key = $sets['key'];
        $value = $sets['value'];
        $data_key = $sets['data_key'];
        $data_value = $sets['data_value'];

		$value_ = str_split($value, 2);
        $save = '';
        $context = "/".$this->getPlaceFor(array('key' => $key, 'value' => $value, 'return' => 'node', 'cache' => True));

        if(strlen($context) == 1) {
            $this->newSpawn(array(
                'key'   => $key,
                'value' => $value
            ));
            $context .= $this->getPlaceFor(array('key' => $key, 'value' => $value, 'return' => 'node', 'cache' => True));
        }

		foreach ($value_ as $key_ => $value_) {
            $save .= "/".$value_;
            if(!is_dir("./".$this->CONFIG['DIR'].$context.$save."/")){
				mkdir("./".$this->CONFIG['DIR'].$context.$save."/", 0777);
			}
        }
        $this->countRequests++;
        if(file_exists("./".$this->CONFIG['DIR'].$context.$save."/".$data_key)){
            @unlink("./".$this->CONFIG['DIR'].$context.$save."/".$data_key);
        }
        else{
            $fp = fopen("./".$this->CONFIG['DIR'].$context.$save."/.history", "a+");
            fputs($fp, " \n \n --- \n TRANSACTION N° ".uniqid()."  ".time()." == ".date('d-m-Y H:i:s T')." VALUE OF `".$data_key."` IS NOW \n \n ".$data_value." \n \n ---");
            fclose($fp);
        }
        $fp = fopen("./".$this->CONFIG['DIR'].$context.$save."/".$data_key, "a+");
        if(fputs($fp, $data_value)) {
            $fp_ = fopen("./".$this->CONFIG['DIR'].$context.$save."/.history", "a+");
            fputs($fp_, " \n \n --- \n TRANSACTION N° ".uniqid()."  ".time()." == ".date('d-m-Y H:i:s T')." VALUE OF `".$data_key."` IS SET \n \n ".$data_value." \n \n ---");
            fclose($fp_);
            $success = True;
        }
        fclose($fp);
        if(isset($success)) {
            return True;
        }
    }
    
    public function unSpawn($sets = array('key' => '', 'value' => '')) {
        $key = $sets['key'];
        $value = $sets['value'];
        $temp_db = new SQLite3('./'.$this->CONFIG['DIR'].'/LIST_ENTRIES.db');
        $temp_db->busyTimeout(10000);
        $regdb_db_blocks = new SQLite3("./".$this->CONFIG['DIR']."/NODES.db");
        $regdb_db_blocks->busyTimeout(10000);  
        $true = 0;
        $db_block = $this->getPlaceFor(array('key' => $key, 'value' => $value, 'return' => 'node', 'cache' => True));
        if($regdb_db_blocks->query('UPDATE LIST_NODES_BY_NAME SET COUNT_ENTRIES = COUNT_ENTRIES - 1 WHERE `NAME` = "'.$db_block.'"')) {
            $true = 1;
        }
        if($temp_db->query('DELETE FROM ENTRIES_BY_KEY_VALUE WHERE `KEY` = "'.$key.'" AND `VALUE` = "'.$value.'"')) {
            $true++;
        }
        if($true == 2) {
            return True;
        }
        else{
            return False;
        }
    }

    public function version() {
        echo '
            <pre>
                --  8""""8 8""""8 8     8""""8 8"""88    8""""8 8""""8   
                --  8    " 8    8 8     8    8 8    8    8    8 8    8   
                --  8e     8eeee8 8e    8eeee8 8    8    8e   8 8eeee8ee 
                --  88     88   8 88    88   8 8    8    88   8 88     8 
                --  88   e 88   8 88    88   8 8    8    88   8 88     8 
                --  88eee8 88   8 88eee 88   8 8eeee8    88eee8 88eeeee8 
                --   
                TrogonCalao DB version 1.0.0
                Florian Hourdin
                report bugs here: florian.hourdin59300@gmail.com
            </pre>
        ';
    }

    public function statsDb() {
        $this->endTime = microtime(true);
        $time = $this->endTime-$this->startTime;
        $appreciation = (round($time/$this->countRequests, 5) > 0.00182) ? 'bad' : 'good';
        echo '
            <style>pre{border:2px dashed red;padding:50px 10px 50px 10px;}pre::after{position:relative;top:50px;height:10px;color:white;background-color:red;content:"stats db";}</style>
            <pre>
                --  8""""8 8""""8 8     8""""8 8"""88    8""""8 8""""8   
                --  8    " 8    8 8     8    8 8    8    8    8 8    8   
                --  8e     8eeee8 8e    8eeee8 8    8    8e   8 8eeee8ee 
                --  88     88   8 88    88   8 8    8    88   8 88     8 
                --  88   e 88   8 88    88   8 8    8    88   8 88     8 
                --  88eee8 88   8 88eee 88   8 8eeee8    88eee8 88eeeee8 
                --   
                Execution time: '.round($time, 3).'seconds
                Count requests: '.$this->countRequests.'
                Avg time per request: '.round($time/$this->countRequests, 5).'seconds
                How feeling: '.$appreciation.'
                Date Time: '.date("d-m-Y H:i:s T").'
            </pre>
        ';
    }
}