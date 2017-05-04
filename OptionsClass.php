<?php
require_once('OptionsHelper.php');

/**
 * Class Options
 *
 */
class OptionsClass {

	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * OptionsClass constructor.
	 *
	 * @param null $options
	 */
	public function __construct($options = null) {
		if ($options) {
			$this->setOptions($options);
		}
	}

	/**
	 * Sets the options to the data provided.
	 * DotData will be processed (see OptionsHelper::dotDataToArray)
	 *
	 * Example:
	 *   $class->setOptions(array('a.b.c' => 'something'));
	 *
	 * @param  $options
	 * @return void
	 */
	public function setOptions($options) {
		$this->options = OptionsHelper::dotDataToArray($options);
	}

	/**
	 * Merges the given setting into the global settings array. Replacing values if they are found.
	 * DotData will be processed (see OptionsHelper::dotDataToArray)
	 *
	 * @param  $options
	 * @return void
	 */
	public function updateOptions($options) {
		$options = OptionsHelper::dotDataToArray($options);
		$this->options = ArrayHelper::merge($this->options, $options);
	}

	/**
	 * @return array The whole settings array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * Examples:
	 *   $options = array(
	 *     'a' => array(
	 *       'b' => 'ab-value',
	 *       'c' => 'ac-value,
	 *     'x' => 'x-value'
	 *   );
	 *   $class->updateOption('a', array('d' => 'ad-value'));
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
	public function updateOption($key, $value) {
		$updateSettings = OptionsHelper::dotStringToArray($key, $value);
		$this->updateOptions($updateSettings);
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
	public function setOption($key, $value) {
		$this->unsetOption($key);
		$this->updateOption($key, $value);
	}

	/**
	 * Examples:
	 *   $options = array(
	 *     'a' => array(
	 *       'b' => 'ab-value',
	 *     ),
	 *     'x' => 'x-value'
	 *   );
	 *   ThemeSetup::unsetOption('a.b');
	 *   => $options = array(
	 *     'x' => 'x-value'
	 *   );
	 *
	 * @param $key
	 */
	public function unsetOption($key) {
		$arrayWithNullForDelete = OptionsHelper::dotStringToArray($key, null);
		$this->updateOptions($arrayWithNullForDelete);
		$this->options = ArrayHelper::filterRecursive($this->options, function($value) {
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
	 *   $class->getOption('a.b');
	 *   // return 'ab-value
	 *
	 * @param  string $key They key of the value you want to read out
	 * @param  null   $notFoundValue
	 * @return mixed
	 */
	public function getOption($key, $notFoundValue = null) {
		$array = $this->getOptions();
		$keys = explode('.', $key);
		foreach($keys as $part) {
			if (!is_array($array)) return $notFoundValue;
			if (!isset($array[$part])) return $notFoundValue;
			$array = $array[$part];
		}
		return $array;
	}

}