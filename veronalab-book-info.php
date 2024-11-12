<?php

/**
 * Plugin Name:     Veronalab book info
 * Plugin URI:      https://example.com
 * Plugin Prefix:   VBI
 * Description:     Show Book Info
 * Author:          Ayob Zare
 * Author URI:      https://example.me
 * Text Domain:     book-info
 * Domain Path:     /languages
 * Version:         0.1.0
 */

namespace RabbitExamplePlugin;

use Rabbit\Application;
use Rabbit\Database\DatabaseServiceProvider;
use Rabbit\Logger\LoggerServiceProvider;
use Rabbit\Plugin;
use Rabbit\Redirects\AdminNotice;
use Rabbit\Templates\TemplatesServiceProvider;
use Rabbit\Utils\Singleton;
use Exception;
use League\Container\Container;

use App\Src\VbiPluginBoot;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

/**
 * Class RabbitExamplePlugin
 *
 * @package RabbitExamplePlugin
 */
class RabbitExamplePlugin extends Singleton {
	/**
	 *
	 * @var Container
	 */
	private $application;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->application = Application::get()->loadPlugin( __DIR__, __FILE__, 'config' );
		$this->init();
	}

	public function init() {

		try {

			/**
			 * Load service providers
			 */
			// $this->application->addServiceProvider( RedirectServiceProvider::class );
			$this->application->addServiceProvider( DatabaseServiceProvider::class );
			$this->application->addServiceProvider( TemplatesServiceProvider::class );
			$this->application->addServiceProvider( LoggerServiceProvider::class );
			// Load your own service providers here...

			/**
			 * Activation hooks
			 */
			$this->application->onActivation(
				function () {
					$this->create_table();
				}
			);

			/**
			 * Deactivation hooks
			 */
			$this->application->onDeactivation(
				function () {
					// Clear events, cache or something else
				}
			);

			$this->application->boot(
				function () {
					// $plugin->includes( __DIR__ . '/src', '*.php' );
					new VbiPluginBoot( $this->application );
					$this->application->loadPluginTextDomain();
				}
			);
		} catch ( Exception $e ) {
			/**
			 * Print the exception message to admin notice area
			 */
			add_action(
				'admin_notices',
				function () use ( $e ) {
					AdminNotice::permanent(
						array(
							'type'    => 'error',
							'message' => $e->getMessage(),
						)
					);
				}
			);

			/**
			 * Log the exception to file
			 */
			add_action(
				'init',
				function () use ( $e ) {
					if ( $this->application->has( 'logger' ) ) {
						$this->application->get( 'logger' )->warning( $e->getMessage() );
					}
				}
			);
		}
	}

	/**
	 * Get application
	 *
	 * @return Container
	 */
	public function getApplication() {
		return $this->application;
	}

	/**
	 * Create book_info table
	 */
	public function create_table() {

		if ( ! $this->application->has( 'database' ) ) {
			return;
		}

		$db = $this->application->get( 'database' );

		$schema = $db->schema();

		if ( ! $schema->hasTable( 'book_info' ) ) {
			$schema->create(
				'book_info',
				function ( $table ) {
					$table->bigIncrements( 'ID' );
					$table->bigInteger( 'post_id' )->unique();
					$table->string( 'isbn', 50 );
				}
			);
		} else {
			// update table if need
		}

		update_option( 'vbi_current_dbv', $this->application->config( 'db_version' ), 'no' );
	}
}

/**
 * Returns the main instance of RabbitExamplePlugin.
 *
 * @return RabbitExamplePlugin
 */
function RabbitExamplePlugin() {
	return RabbitExamplePlugin::get();
}

RabbitExamplePlugin();