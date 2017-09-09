<?php
	class Postprocessor {
		protected $config;
		
		function __construct(Config $config = NULL) {
			$this->config = $config;
		}
	
		function do($line) {
			$ret = str_replace("\t", $this->config->get('output_indent'), $line);
			return $ret;
		}
	}