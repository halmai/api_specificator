<?php
	$fileInName = '../test/api_spec.as';
	$fileOutName = $fileInName.'.yaml';
	
	spl_autoload_register(
		function ($className) {
		$fileName = 'class/'. strtolower($className). '.php';
		include($fileName);
	});
	
	$in = new Input($fileInName);
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
	