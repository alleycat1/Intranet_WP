<?php
/**
 * This file stores pricing of the licensing plans.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MO_LDAP_License_Plans_Pricing' ) ) {
	/**
	 * MO_LDAP_License_Plans_Pricing : Class for pricing of the licensing plans.
	 */
	class MO_LDAP_License_Plans_Pricing {

		/**
		 * Var pricing_custom_profile
		 *
		 * @var array
		 */
		public $pricing_custom_profile;
		/**
		 * Var pricing_kerberos
		 *
		 * @var array
		 */
		public $pricing_kerberos;
		/**
		 * Var pricing_standard
		 *
		 * @var array
		 */
		public $pricing_standard;
		/**
		 * Var pricing_enterprise
		 *
		 * @var array
		 */
		public $pricing_enterprise;
		/**
		 * Var subsite_intances
		 *
		 * @var array
		 */
		public $subsite_intances;
		/**
		 * Var mulpricing_custom_profile
		 *
		 * @var array
		 */
		public $mulpricing_custom_profile;
		/**
		 * Var mulpricing_kerberos
		 *
		 * @var array
		 */
		public $mulpricing_kerberos;
		/**
		 * Var mulpricing_standard
		 *
		 * @var array
		 */
		public $mulpricing_standard;
		/**
		 * Var mulpricing_enterprise
		 *
		 * @var array
		 */
		public $mulpricing_enterprise;

		/**
		 * __construct
		 *
		 * @return void
		 */
		public function __construct() {
			$this->pricing_custom_profile = array(
				'1'         => '199',
				'2'         => '358',
				'3'         => '507',
				'4'         => '646',
				'5'         => '775',
				'6'         => '889',
				'7'         => '988',
				'8'         => '1,072',
				'9'         => '1,141',
				'10'        => '1,195',
				'11'        => '1,244',
				'12'        => '1,288',
				'13'        => '1,327',
				'14'        => '1,361',
				'15'        => '1,390',
				'16'        => '1,415',
				'17'        => '1,434',
				'18'        => '1,448',
				'19'        => '1,457',
				'20'        => '1,462',
				'30'        => '1,512',
				'40'        => '1,562',
				'50'        => '1,612',
				'100'       => '1,762',
				'UNLIMITED' => '1,999',
			);

			$this->pricing_kerberos = array(
				'1'         => '299',
				'2'         => '538',
				'3'         => '672',
				'4'         => '971',
				'5'         => '1,164',
				'6'         => '1,336',
				'7'         => '1,484',
				'8'         => '1,611',
				'9'         => '1,714',
				'10'        => '1,796',
				'11'        => '1,869',
				'12'        => '1,935',
				'13'        => '1,994',
				'14'        => '2,045',
				'15'        => '2,088',
				'16'        => '2,126',
				'17'        => '2,155',
				'18'        => '2,176',
				'19'        => '2,189',
				'20'        => '2,197',
				'30'        => '2,272',
				'40'        => '2,347',
				'50'        => '2,422',
				'100'       => '2,647',
				'UNLIMITED' => '2,999',
			);

			$this->pricing_standard = array(
				'1'         => '399',
				'2'         => '718',
				'3'         => '1,017',
				'4'         => '1,295',
				'5'         => '1,554',
				'6'         => '1,782',
				'7'         => '1,981',
				'8'         => '2,149',
				'9'         => '2,288',
				'10'        => '2,396',
				'11'        => '2,494',
				'12'        => '2,582',
				'13'        => '2,661',
				'14'        => '2,729',
				'15'        => '2,787',
				'16'        => '2,837',
				'17'        => '2,875',
				'18'        => '2,903',
				'19'        => '2,921',
				'20'        => '2,931',
				'30'        => '3,032',
				'40'        => '3,132',
				'50'        => '3,232',
				'100'       => '3,533',
				'UNLIMITED' => '3,999',
			);

			$this->pricing_enterprise = array(
				'1'         => '499',
				'2'         => '898',
				'3'         => '1,271',
				'4'         => '1,620',
				'5'         => '1,943',
				'6'         => '2,229',
				'7'         => '2,477',
				'8'         => '2,688',
				'9'         => '2,861',
				'10'        => '2,997',
				'11'        => '3,119',
				'12'        => '3,230',
				'13'        => '3,328',
				'14'        => '3,413',
				'15'        => '3,485',
				'16'        => '3,548',
				'17'        => '3,596',
				'18'        => '3,631',
				'19'        => '3,653',
				'20'        => '3,666',
				'30'        => '3,791',
				'40'        => '3,917',
				'50'        => '4,042',
				'100'       => '4,418',
				'UNLIMITED' => '4,999',
			);

			$this->mulpricing_custom_profile = array(
				'1'         => '199',
				'2'         => '358',
				'3'         => '507',
				'4'         => '646',
				'5'         => '775',
				'6'         => '889',
				'7'         => '988',
				'8'         => '1,072',
				'9'         => '1,141',
				'10'        => '1,195',
				'11'        => '1,244',
				'12'        => '1,288',
				'13'        => '1,327',
				'14'        => '1,361',
				'15'        => '1,390',
				'16'        => '1,415',
				'17'        => '1,434',
				'18'        => '1,448',
				'19'        => '1,457',
				'20'        => '1,462',
				'30'        => '1,512',
				'40'        => '1,562',
				'50'        => '1,612',
				'100'       => '1,762',
				'UNLIMITED' => '1,999',
			);

			$this->mulpricing_kerberos = array(
				'1'         => '299',
				'2'         => '538',
				'3'         => '672',
				'4'         => '971',
				'5'         => '1,164',
				'6'         => '1,336',
				'7'         => '1,484',
				'8'         => '1,611',
				'9'         => '1,714',
				'10'        => '1,796',
				'11'        => '1,869',
				'12'        => '1,935',
				'13'        => '1,994',
				'14'        => '2,045',
				'15'        => '2,088',
				'16'        => '2,126',
				'17'        => '2,155',
				'18'        => '2,176',
				'19'        => '2,189',
				'20'        => '2,197',
				'30'        => '2,272',
				'40'        => '2,347',
				'50'        => '2,422',
				'100'       => '2,647',
				'UNLIMITED' => '2,999',
			);

			$this->mulpricing_standard = array(
				'1'         => '399',
				'2'         => '718',
				'3'         => '1,017',
				'4'         => '1,295',
				'5'         => '1,554',
				'6'         => '1,782',
				'7'         => '1,981',
				'8'         => '2,149',
				'9'         => '2,288',
				'10'        => '2,396',
				'11'        => '2,494',
				'12'        => '2,582',
				'13'        => '2,661',
				'14'        => '2,729',
				'15'        => '2,787',
				'16'        => '2,837',
				'17'        => '2,875',
				'18'        => '2,903',
				'19'        => '2,921',
				'20'        => '2,931',
				'30'        => '3,032',
				'40'        => '3,132',
				'50'        => '3,232',
				'100'       => '3,533',
				'UNLIMITED' => '3,999',
			);

			$this->mulpricing_enterprise = array(
				'1'         => '499',
				'2'         => '898',
				'3'         => '1,271',
				'4'         => '1,620',
				'5'         => '1,943',
				'6'         => '2,229',
				'7'         => '2,477',
				'8'         => '2,688',
				'9'         => '2,861',
				'10'        => '2,997',
				'11'        => '3,119',
				'12'        => '3,230',
				'13'        => '3,328',
				'14'        => '3,413',
				'15'        => '3,485',
				'16'        => '3,548',
				'17'        => '3,596',
				'18'        => '3,631',
				'19'        => '3,653',
				'20'        => '3,666',
				'30'        => '3,791',
				'40'        => '3,917',
				'50'        => '4,042',
				'100'       => '4,418',
				'UNLIMITED' => '4,999',
			);

			$this->subsite_intances = array(
				'Number of Subsites'                   => '0',
				'$60 - Upto 3 Subsites / Instance'     => '60',
				'$90 - Upto 5 Subsites / Instance'     => '90',
				'$160 - Upto 10 Subsites / Instance'   => '160',
				'$200 - Upto 15 Subsites / Instance'   => '200',
				'$240 - Upto 20 Subsites / Instance'   => '240',
				'$300 - Upto 30 Subsites / Instance'   => '300',
				'$360 - Upto 40 Subsites / Instance'   => '360',
				'$400 - Upto 50 Subsites / Instance'   => '400',
				'$500 - Upto 100 Subsites / Instance'  => '500',
				'$550 - Upto 200 Subsites / Instance'  => '550',
				'$600 - Upto 300 Subsites / Instance'  => '600',
				'$650 - Upto 400 Subsites / Instance'  => '650',
				'$700 - Upto 500 Subsites / Instance'  => '700',
				'$999 - Unlimited Subsites / Instance' => '999',
			);

		}
	}
}
