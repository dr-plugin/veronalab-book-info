<?php
namespace App\Src\Admin;

defined( 'ABSPATH' ) || exit;

use App\Src\VbiPluginBoot;
use Rabbit\Utils\Singleton;

class VbiAddMetaBox extends Singleton {

	public function __construct() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post_book', array( __CLASS__, 'save_book_isbn' ) );
	}

	public static function add_meta_box( $post_type ) {
		if ( 'book' === $post_type ) {
			add_meta_box(
				'isbn-number',      // Unique ID
				__( 'isbn number', 'book-info' ),
				array( __CLASS__, 'showInput' ),  // Callback function
			);
		}
	}

	public static function showInput( $post ) {
		$db  = VbiPluginBoot::db();
		$row = $db->table( 'book_info' )->where( 'post_id', $post->ID )->first();

		if ( ! isset( $row->isbn ) ) {
			$row = '';
		} else {
			$row = $row->isbn;
		}

		echo VbiPluginBoot::view( 'admin/book-metabox', array( 'data' => $row ) );
	}

	public static function save_book_isbn( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['book-isbn'] ) ) {
			
			$isbn = sanitize_text_field( $_POST['book-isbn'] );

			$db = VbiPluginBoot::db();
			$db->table( 'book_info' )->updateOrInsert(
				array( 'post_id' => $post_id ),
				array(
					'post_id' => $post_id,
					'isbn'    => $isbn,
				)
			);
		}
	}
}
