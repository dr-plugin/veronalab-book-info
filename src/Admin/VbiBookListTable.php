<?php
namespace App\Src\Admin;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use WP_List_Table;
use App\src\VbiPluginBoot;

class VbiBookListTable extends WP_List_Table {

	public function prepare_items() {

		$page = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT ) ?? 1;
		$page = max( 1, $page );

		$count = $this->get_row_count();

		$per_page = get_user_option( 'book_info_par_page', get_current_user_id() );
		$per_page = $per_page ?? 10;

		$this->_column_headers = array( $this->get_columns(), $this->get_hidden_columns(), array(), 'ID' );

		$this->set_pagination_args(
			array(
				'total_items' => $count,
				'per_page'    => $per_page,
				'total_pages' => ceil( $count / $per_page ),
			)
		);

		$offset      = ( $page - 1 ) * $per_page;
		$this->items = $this->get_book_info( $per_page, $offset );
	}

	public function get_row_count() {
		return VbiPluginBoot::db()->table( 'book_info' )->count();
	}

	/**
	 * fetch data by limit and offset
	 */
	public function get_book_info( int $limit, int $offset ) {

		$rows = VbiPluginBoot::db()->table( 'book_info' )->skip( $offset )->take( $limit )->get()->all();

		if ( is_null( $rows ) ) {
			return '';
		}

		$output = array();
		foreach ( $rows as $row ) {
			$output[] = array(
				'ID'      => $row->ID,
				'post_id' => $row->post_id,
				'isbn'    => $row->isbn,
			);
		}

		return $output;
	}

	public function column_default( $item, $column_name ) {
		if ( isset( $item[ $column_name ] ) ) {
			return $item[ $column_name ];
		}

		return '-';
	}

	public function get_columns() {
		return array(
			'ID'      => esc_html__( 'id', 'book-info' ),
			'post_id' => esc_html__( 'post id', 'book-info' ),
			'isbn'    => esc_html__( 'isbn number', 'book-info' ),
		);
	}

	/**
	 * handle hidden row
	 */
	public function get_hidden_columns() {
		$screen    = get_current_screen();
		$screen_id = $screen->id;

		$hidden = get_user_option( 'manage' . $screen_id . 'columnshidden' );

		if ( empty( $hidden ) ) {
			return array();
		}

		return $hidden;
	}
}
