<?php
namespace App\Src;

defined( 'ABSPATH' ) || exit;

use App\Src\Admin\VbiAddMetaBox;
use App\Src\Admin\VbiAdminMenu;

class VbiPluginBoot {

	protected static $application;

	public function __construct( $application ) {
		self::$application = $application;

		if ( is_admin() ) {
			VbiAddMetaBox::get();
			VbiAdminMenu::get();
		}

		add_action(
			'init',
			function () {
				VbiRegisterBook::get();
			}
		);
	}

	public static function db() {
		if ( self::$application->has( 'database' ) ) {
			return self::$application->get( 'database' );
		}

		return false;
	}

	public static function view( $file_name, $val = array() ) {
		if ( self::$application->has( 'template' ) ) {
			return self::$application->template( $file_name, $val );
		}
		return 'template service provider not loaded';
	}
}
