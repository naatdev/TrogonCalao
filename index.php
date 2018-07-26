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

    public function __construct($SETTINGS = array('DIR' => 'DATA')) {
        $this->CONFIG = $SETTINGS;
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

		if(file_exists($context)){
			$fp = file_get_contents($context, "r");
			return $fp;
		}
		else{
			return False;
		}
    }
    
    public function setDataFor($sets = array('key' => '', 'value' => '', 'data_key' => '', 'data_value' => '', 'edit' => False)) {
		$key = $sets['key'];
        $value = $sets['value'];
        $data_key = $sets['data_key'];
        $data_value = $sets['data_value'];
        $edit = $sets['edit'];

		$value_ = str_split($value, 2);
        $save = '';
        $context = "/".$this->getPlaceFor(array('key' => $key, 'value' => $value, 'return' => 'node', 'cache' => True));

		foreach ($value_ as $key_ => $value_) {
            $save .= "/".$value_;
            if(!is_dir("./".$this->CONFIG['DIR'].$context.$save."/")){
				mkdir("./".$this->CONFIG['DIR'].$context.$save."/", 0777);
			}
        }

		if(file_exists("./".$this->CONFIG['DIR'].$context.$save."/".$data_key) AND $edit != True){
			return False;
		}
		else{
			if($edit == True){
				@unlink("./".$this->CONFIG['DIR'].$context.$save."/".$data_key);
			}
			$fp = fopen("./".$this->CONFIG['DIR'].$context.$save."/".$data_key, "a+");
			if(fputs($fp, $data_value)) {
                return True;
            }
			fclose($fp);
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
}