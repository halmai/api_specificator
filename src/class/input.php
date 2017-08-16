<?php

	class Input {
		protected $handler;
		
		protected $lineIndex;
		
		function __construct($fileName) {
			$this->handler = fopen($fileName, 'r');
			if (!$this->handler) {
				throw new Exception('File opening error. Filename='. $fileName);
			}
			$this->lineIndex = 0;
		}
		
		public function throwError($msg) {
			throw new Exception($msg. ' Line#'. $this->lineIndex);
		}
		
		function getLine() {
			if (feof($this->handler)) {
				return NULL;
			}
			$this->lineIndex++;
			$line = fgets($this->handler);
			if ($line === FALSE) {
				$this->throwError('File reading error.');
			}
			return $line;
		}
		
		function getMatchingLineOrFail($regExp, $skipWhitespaceLines = TRUE) {
			do {
				$line = $this->getLine();
				
				if ($line === NULL) {
					$this->throwError('Unexpected end of file. Expected: '. $regExp);
				}
				if ($skipWhitespaceLines and trim($line) === "") {
					continue;
				}
				
				$res = preg_match($regExp, $line, $matches);
				
				if ($res === 0) {
					$this->throwError('Unexpected line.');
				}
				
				if ($res === FALSE) {
					$this->throwError('Regexp error.'. $regExp);
				}
				
				return $matches;
			} while (TRUE);
		}
		
		function close() {
			fclose($this->handler);
		}
	}
