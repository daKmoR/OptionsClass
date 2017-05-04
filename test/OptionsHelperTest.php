<?php
	require_once(__DIR__ . '/../OptionsHelper.php');

	class OptionsHelperTest extends PHPUnit_Framework_TestCase {

		/**
		 * @param $input
		 * @param $expectedResult
		 *
		 * @dataProvider providerTestArrayToDotString
		 */
		public function testArrayToDotString($input, $expectedResult) {
			$result = OptionsHelper::arrayToDotString($input);

			$this->assertEquals($expectedResult, $result);
		}

		public function providerTestArrayToDotString() {
			return array(
				array(
					array(
						'b' => array(
							'ba' => 'ba-value'
						)
					),
					'b.ba'
				),

				array(
					array(
						'a.ab.abc' => array(
							'abcd' => 'abcd-value'
						)
					),
					'a.ab.abc.abcd'
				)

			);
		}

	}