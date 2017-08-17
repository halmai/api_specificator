<?php
	class Postprocessor {
		function do($line) {
			$ret = str_replace("\t", '    ', $line);
			return $ret;
		}
	}