<?php
	if (!defined('BOWER_PATH')) {
		define('BOWER_PATH', __DIR__ . '/..');
	}

	require_once(BOWER_PATH . '/Is/Is.php');
	require_once(BOWER_PATH . '/ArrayHelper/ArrayHelper.php');

	/**
	 * Class Options
	 *
	 */
	class OptionsHelper {

		/**
		 * @var array
		 */
		static $options = array();

		/**
		 * Sets the options to the data provided.
		 * DotData will be processed (see static::dotDataToArray)
		 *
		 * Example:
		 *   OptionsHelper::setOptions(array('a.b.c' => 'something'));
		 *
		 * @param  $options
		 * @return void
		 */
		public static function setOptions($options) {
			static::$options = static::dotDataToArray($options);
		}

		/**
		 * Merges the given setting into the global settings array. Replacing values if they are found.
		 * DotData will be processed (see static::dotDataToArray)
		 *
		 * @param  $options
		 * @return void
		 */
		public static function updateOptions($options) {
			$options = static::dotDataToArray($options);
			static::$options = ArrayHelper::merge(static::$options, $options);
		}

		public static function updateUndefinedOptions($options) {
			$options = static::dotDataToArray($options);
			static::$options = ArrayHelper::mergeOnlyUndefined(static::$options, $options);
		}

		/**
		 * @return array The whole settings array
		 */
		public static function getOptions() {
			return static::$options;
		}

		/**
		 * Examples:
		 *   $options = array(
		 *     'a' => array(
		 *       'b' => 'ab-value',
		 *       'c' => 'ac-value,
		 *     'x' => 'x-value'
		 *   );
		 *   OptionsHelper::updateOption('a', array('d' => 'ad-value'));
		 *   => $options = array(
		 *     'a' => array(
		 *       'b' => 'ab-value',
		 *       'c' => 'ac-value,
		 *       'd' => 'ad-value',
		 *     'x' => 'xvalue'
		 *   );
		 *
		 * @param        $key
		 * @param  mixed $value The value you want to set
		 */
		public static function updateOption($key, $value) {
			$updateSettings = static::dotStringToArray($key, $value);
			static::updateOptions($updateSettings);
		}

		/**
		 * Examples:
		 *   $options = array(
		 *     'a' => array(
		 *       'b' => 'ab-value',
		 *       'c' => 'ac-value,
		 *     'x' => 'x-value'
		 *   );
		 *   OptionsHelper::setOption('a', array('d' => 'ad-value'));
		 *   => $options = array(
		 *     'a' => array(
		 *       'd' => 'ad-value',
		 *     'x' => 'x-value'
		 *   );
		 *
		 * @param        $key
		 * @param  mixed $value The value you want to set
		 */
		public static function setOption($key, $value) {
			static::unsetOption($key);
			static::updateOption($key, $value);
		}


		/**
		 * Examples:
		 *   $options = array(
		 *     'a' => array(
		 *       'b' => 'ab-value',
		 *     'x' => 'x-value'
		 *   );
		 *   OptionsHelper::unsetOption('b');
		 *   => $options = array(
		 *     'x' => 'x-value'
		 *   );
		 *
		 * @param $key
		 */
		public static function unsetOption($key) {
			$arrayWithNullForDelete = static::dotStringToArray($key, null);
			static::updateOptions($arrayWithNullForDelete);
			static::$options = ArrayHelper::filterRecursive(static::$options, function($value) {
				return !($value === null || is_array($value) && count($value) === 0);
			});
		}

		/**
		 * Get an Option Value using dot notation for multidimensional arrays.
		 *
		 * Examples:
		 *   $options = array(
		 *     'a' => array(
		 *       'b' => 'ab-value',
		 *     'x' => 'x-value'
		 *   );
		 *   OptionsHelper::getOption('a.b');
		 *   // return 'ab-value
		 *
		 * @param  string $key            They key of the value you want to read out
		 * @param  null   $notFoundValue  Return Value if no option is found
		 * @return mixed
		 */
		public static function getOption($key, $notFoundValue = null) {
			$array = static::getOptions();
			$keys = explode('.', $key);
			foreach($keys as $part) {
				if (!is_array($array)) return $notFoundValue;
				if (!isset($array[$part])) return $notFoundValue;
				$array = $array[$part];
			}
			return $array;
		}

		/**
		 * OptionsHelper::dotStringToArray('a.b.c', 'abc-value');
		 * => array('a' => array('b' => array('c' => 'abc-value')));
		 *
		 * @param $dataKey
		 * @param $dataValue
		 * @return array
		 */
		public static function dotStringToArray($dataKey, $dataValue) {
			if (is_array($dataValue)) {
				$dataValue = static::dotDataToArray($dataValue);
			}
			if (strpos($dataKey, '.') !== false) {
				$keys = explode('.', $dataKey);
				$partArray = array();
				foreach (array_reverse($keys) as $i => $part) {
					if ($i === 0) {
						$partArray = array($part => $dataValue);
					} else {
						$partArray = array($part => $partArray);
					}
				}
				return $partArray;
			}
			return array($dataKey => $dataValue);
		}

		/**
		 * Converts an array with dot separated keys to an multidimensional array
		 *
		 * Example:
		 *   OptionsHelper::dotDataToArray(array(
		 *     'a' => array('b.c' => 'abc-value')
		 *     'x.y' => 'xy-value',
		 *     'x.z' => 'xz-value'
		 *   ));
		 *   =>
		 *   array(
		 *     'a' => array('b' => array('c' => 'abc-value')),
		 *     'x' => array(
		 *       'y' => 'xy-value',
		 *       'z' => 'xz-value'
		 *      )
		 *   )
		 *
		 * @param $data
		 * @return array
		 */
		public static function dotDataToArray($data) {
			if (Is::emptyArray($data)) {
				return array();
			}
			$returnArray = array();
			foreach($data as $dataKey => $dataValue) {
				if (is_array($dataValue)) {
					$dataValue = static::dotDataToArray($dataValue);
				}
				if (strpos($dataKey, '.') !== false) {
					$mergeArray = static::dotStringToArray($dataKey, $dataValue);
				} else {
					$mergeArray = array($dataKey => $dataValue);
				}
				$returnArray = ArrayHelper::merge($returnArray, $mergeArray);
			}
			return $returnArray;
		}

		public static function arrayToDotString($array) {
			$dotString = '';
			while(Is::notEmptyArray($array)) {
				$dotString .= (string) current(array_keys($array)) . '.';
				$array = array_shift($array);
			}
			return substr($dotString, 0, -1);
		}

		public static function dotStringModifyLevel($dotString, $level = 1) {
			while ($level !== 0) {
				$found = $level < 0 ? strrpos($dotString, '.') : strpos($dotString, '.');
				if (is_numeric($found) && $found > 0) {
					$dotString = $level < 0 ? substr($dotString, 0, $found) : substr($dotString, $found+1);
					$level += $level < 0 ? 1 : -1;
				} else {
					return '';
				}
			}
			return $dotString;
		}

		public static function dotStringLevel($dotString, $level = 1) {
			$return = $dotString;
			while ($level !== 0) {
				$found = $level < 0 ? strrpos($dotString, '.') : strpos($dotString, '.');
				if (is_numeric($found) && $found > 0) {
					$return = $level < 0 ? substr($dotString, $found+1) : substr($dotString, 0, $found);
					$dotString = $level < 0 ? substr($dotString, 0, $found) : substr($dotString, $found+1);
					$level += $level < 0 ? 1 : -1;
				} else {
					return '';
				}
			}
			return $return;
		}

		public static function getParentStack($searchString, $stack) {
			$return = array();
			foreach ($stack as $key => $value) {
				if (is_array($value)) {
					if (strpos($key, $searchString) !== false) {
						$return[$key] = $value;
					} else {
						$stack = static::getParentStack($searchString, $value);
						if (is_array($stack) && !empty($stack)) {
							$return[$key] = $stack;
						}
					}
				} else {
					if (strpos($value, $searchString) !== false) {
						$return[$key] = $value;
					}
				}
			}
			return empty($return) ? false: $return;
		}

	}