<?php
	class Config {
		protected $config = [
			'mode'            => 'default',   // 'help' => display help and die(), 'default' => normal conversion
			'input_filename'  => NULL,
			'output_filename' => NULL,        // NULL means stdout
		
		];
		protected $args;
		
		function __construct() {
			$this->initFromCommandLineArgs();
		}
		
		protected function initFromCommandLineArgs() {
			global $argv;
			$this->args = $argv;
			
			$this->getNextArg(); // skip the name of this script
			
			while (($arg = $this->getNextArg()) !== NULL) {
				if (in_array($arg, ['-h', '--help'])) {
					$this->config['mode'] = 'help';
				} elseif (in_array($arg, ['-o', '--output'])) {
					$this->config['output_filename'] = $this->getNextArg();
				} elseif (substr($arg, 0, 1) !== '-') {
					if ($this->config['input_filename'] === NULL) {
						$this->config['input_filename'] = $arg;
					} else {
						$this->config['mode'] = 'help';
						return;
					}
				} else {   // something unknown starting with '-...'
					$this->config['mode'] = 'help';
					return;
				}
			}
			
			if ($this->config['input_filename'] === NULL) {
				$this->config['mode'] = 'help';
				return;
			}
		}
		
		protected function getNextArg() {
			if (count($this->args) === 0) {
				return NULL;
			}
			$head = $this->args[0];
			array_shift($this->args);
			return $head;
		}
		
		function get($name) {
			return $this->config[$name];
		}
	}