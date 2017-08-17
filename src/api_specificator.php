<?php
	spl_autoload_register(
		function ($className) {
		$fileName = 'class/'. strtolower($className). '.php';
		include($fileName);
	});
	
	function displayHelp() {
		$msg = <<<EOT
API Specificator - preprocessor for creating swagger files.
Usage: 
	php api_specificator.php <commands> <input file>
Commands:
	-h, --help                display help 
	-o, --output <filename>   define output file (default is input filename + '.yaml' extension          
Example:
	convert api_spec.as file into api_spec.yaml
		php api_specificator.php -o api_spec.yaml api_spec.as
	
	convert api_spec.as file into api_spec.as.yaml 
		php api_specificator.php -o api_spec.yaml api_spec.as
EOT;
		print $msg;
	}
	
	
	$config = new Config();

	if ($config->get('mode') === 'help') {
		displayHelp();
		die();
	}
	
	$in = new Input($config->get('input_filename'));
	$postprocessor = new Postprocessor();
	$parser = new Parser($in);
	
	while (($line = $in->getLine()) !== NULL) {
		$lines = [$line];
		if ($parser->isKnown($line)) {
			$lines = $parser->process();
		}
		foreach ($lines as $line) {
			print $postprocessor->do($line);
		}
	}
	
	$in->close();
	