<?php

namespace MadeByDavid;

class WordpressSkeletonConfigSalter {

	const WP_CONFIG_FILE = 'wp-config.php';
	const EMPTY_SALTS_FILE = 'emptySalts.txt';
	const SALTS_URL = 'https://api.wordpress.org/secret-key/1.1/salt';

	private static function getWPConfigFilename() {
		return getcwd().'/'.self::WP_CONFIG_FILE;
	}

	private static function getEmptySaltsFilename() {
		return dirname(__FILE__).'/../../res/'.self::EMPTY_SALTS_FILE;
	}

	public static function salt() {


		if (false === file_exists($configFile = self::getWPConfigFilename())) {
			throw new \Exception('WP config file - '.$configFile.' not found.');
		}

		if (0 === strlen($configFileContents = file_get_contents($configFile))) {
			throw new \Exception('WP config file is empty.');
		}


		if (false === file_exists($emptySaltsFile = self::getEmptySaltsFilename())) {
			throw new \Exception('Empty salts file - '.$emptySaltsFile.' not found.');
		}

		if (0 === strlen($emptySaltsFileContents = file_get_contents($emptySaltsFile))) {
			throw new \Exception('Empty salts file is empty.');
		}

		if (0 === strlen($newSalts = file_get_contents(self::SALTS_URL))) {
			throw new \Exception('Unable to get WP salts from '.self::SALTS_URL);
		}

		if ($configFileContents === ($replacedConfigFileContents = str_replace($emptySaltsFileContents, $newSalts, $configFileContents))) {
			throw new \Exception('Empty salts were not found in the WP config file.');
		}

		if (false === file_put_contents($configFile, $replacedConfigFileContents)) {
			throw new Exception('Unable to write to WP config file');
		}

		return true;


	}

}
