<?php
namespace App\Src\Admin;

defined( 'ABSPATH' ) || exit;

use Rabbit\Utils\Singleton;

class VbiAdminMenu extends Singleton {

	public $plugin_page;
	protected $table;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_filter( 'set-screen-option', array( $this, 'save_screen_option' ), 10, 3 );
	}

	public function save_screen_option( $status, $option, $value ) {
		if ( 'book_info_par_page' === $option ) {
			return $value;
		}
		return $status;
	}

	public function admin_menu() {
		$this->plugin_page = add_menu_page(
			'books info',
			esc_html__( 'books info', 'book-info' ),
			'manage_options',
			'books-info',
			array( $this, 'show_book_info_data' ),
			'dashicons-book',
			50
		);

		add_action( "load-$this->plugin_page", array( $this, 'init_list_table' ) );
	}

	public function init_list_table() {
		add_screen_option(
			'per_page',
			array(
				'label'   => 'Show on page',
				'default' => 10,
				'option'  => 'book_info_par_page',
			)
		);

		$this->table = new VbiBookListTable();
	}


	public function show_book_info_data() {

		$this->table->prepare_items();

		echo '<div class="wrap"><h1>' . esc_html__( 'Book Info', 'book-info' ) . '</h1>';

		$this->table->display();

		echo '</div>';
	}
}
