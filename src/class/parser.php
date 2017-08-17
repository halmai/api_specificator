<?php
	
	class Parser {
		protected $lead;
		protected $type;
		protected $outBuffer;
		protected $in;
		protected $tab = "\t";
		protected $eol = NULL;  // end-of-line, coming from the first line-ending
		
		public function __construct(Input $in) {
			$this->in = $in;
		}
		
		public function isKnown($line) {
			if ($this->eol === NULL) {
				$this->eol = "\n"; // todo: should be trimmed from the end of $line
			}
			$re = '/^(\s*)(request_template):.*$/';
			if (preg_match($re, $line, $matches)) {
				$this->lead = $matches[1];
				$this->type = $matches[2];
				return TRUE;
			}
			return FALSE;
		}
		
		public function process() {
			$this->outBuffer = [];
			switch ($this->type) {
				case 'request_template':
					$this->processRequestTemplate();
					break;
				
				default:
					throw new Exception('Unknown type in Parser.');
			}
			return $this->outBuffer;
		}
		
		protected function spit($line) {
			$this->outBuffer[] = $line. $this->eol;
		}
		
		protected function indent($num) {
			return $this->lead. str_repeat($this->tab, $num);
		}
		
		protected function processRequestTemplate() {
			$regExp = '/^\s*{\s*$/';
			$this->in->getMatchingLineOrFail($regExp);
			
			$isDone = FALSE;
			$properties = [];
			do {
				$line = $this->in->getLine();
				$trim = trim($line);
				if ($trim === '') {
					continue;
				}
				if ($trim === '}') {
					break;
				}
				
				$property = $this->processRequestTemplateLine($trim);
				$properties[] = $property;
			} while (!$isDone);
			
			$this->spit($this->indent(0). 'schema:');
			$this->spit($this->indent(1). 'type: object');
			$this->spit($this->indent(1). 'properties:');
			foreach ($properties as $property) {
				$this->spit($this->indent(2). $property['name']. ':');
				$this->spit($this->indent(3). 'type: '. $property['type']);
				if ($property['summary']) {
					$this->spit($this->indent(3). 'description: '. $property['summary']);
				}
			}
			$this->spit($this->indent(1). 'example:');
			foreach ($properties as $property) {
				$this->spit($this->indent(2). $property['name']. ': '. $property['example']);
			}
			
			$isFirstRequired = TRUE;
			foreach ($properties as $property) {
				if ($property['isRequired']) {
					if ($isFirstRequired) {
						$isFirstRequired = FALSE;
						$this->spit($this->indent(1). 'required:');
					}
					$this->spit($this->indent(2). '- '. $property['name']);
				}
			}
		}
		
		/**
		 *
		 * Guesses the type details based on the template.
		 *
		 * $line string - the part of the template from...
		 *   *email:  "johndoe@gmail.com"  // email address of the user
		 *            ^
		 *            +---- ...this character (after ":[\s]+")
		 *
		 * - "..." => string
		 * - "...@..." => email
		 * - bool => boolean
		 * - ....
		 *
		 * @param $line
		 * @return array
		 */
		protected function guessTypeDetailsFromTemplate($line) {
			if (substr($line, 0, 1) === '"') {
				if (!preg_match('/^("[^"]+")(\s+\/\/(.*))?$/', $line, $matches)) {
					$this->in->throwError('Wrong string template');
				}
				$example = $matches[1];
				$summary = isset($matches[3]) ? $matches[3] : NULL;
				$type =
					strpos($example, '@') > 0 ?
					'string' :   // maybe 'email' will also be supported once...
					'string';
			} else {
				if (!preg_match('/^([^\s]+)(\s+\/\/(.*))?$/', $line, $matches)) {
					$this->in->throwError('Wrong string template');
				}
				$example = $matches[1];
				$summary = isset($matches[3]) ? $matches[3] : NULL;
				if (in_array($example, ['true', 'false'])) {
					$type = 'boolean';
				} elseif (preg_match('/^\d+$/', $example)) {
					$type = 'integer';
				} elseif (preg_match('/^[0-9eE\.\+\-]+$/', $example)) {
					$type = 'number';
				} else {
					$type = 'string';
				}
			}
			
			return [
				'type' => $type,
				'summary' => $summary,
				'example' => $example,
			];
		}
		
		protected function processRequestTemplateLine($line) {
			$isRequired = FALSE;
			if (substr($line, 0, 1) === '*') {
				$isRequired = TRUE;
				$line = substr($line, 1);
			}
			
			if (!preg_match('/^([a-zA-Z0-9_]+):\s*(.*)$/', $line, $matches)) {
				$this->in->throwError('Missing or incorrect property name');
			}
			
			$propertyName = $matches[1];
			$line = $matches[2];
			
			$typeDetails = [];
			if (substr($line, 0, 1) === '[') {
				//$typeDetails = $this->getTypeDetailsFromDescriptor($line);
				//$line = $typeDetails->rest;
				$this->in->throwError('TODO: [...] doesn\'t work yet');
			}

			$guessedTypeDetails = $this->guessTypeDetailsFromTemplate($line);
			$typeDetails = array_merge($guessedTypeDetails, $typeDetails);  // the [...] overwrites the guessed details
			
			return [
				'name'       => $propertyName,
				'isRequired' => $isRequired,
				'example'    => $typeDetails['example'],
				'type'       => $typeDetails['type'],
				'summary'    => $typeDetails['summary'],
			];
		}
	}