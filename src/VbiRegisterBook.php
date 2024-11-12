<?php
namespace App\Src;

defined('ABSPATH') || exit;

use Rabbit\Utils\Singleton;

class VbiRegisterBook extends Singleton 
{

    public function __construct()
    {
        $this->register_book();
        $this->register_authers();
        $this->register_publisher();
    }

    public function register_book()
    {
        register_post_type(
            'book',
            array(
                'labels' => array(
                    'name' => __('Books', 'vsp-text-domain'),
                    'singular_name' => __('Book', 'vsp-text-domain'),
                ),
                'menu_icon'   => 'dashicons-book',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu'       => true,
                'exclude_from_search' => true,
                'show_in_nav_menus' => true,
                'has_archive' => true,
                'rewrite' => true,
                //'supports' => array('title',  'add-media', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
                'supports' => array('title',  'add-media', 'editor', 'thumbnail')
            )
        );
    }

    public function register_authers()
    {
        //Authors
        register_taxonomy('Authors', array('book'), [
            'hierarchical' => true,
            'labels'       => [
                'name'              => __('authors', 'vsp-text-domain'),
            ],
        ]);
    }

    public function register_publisher()
    {
        //publisher
        register_taxonomy('publisher', array('book'), [
            'hierarchical' => true,
            'labels'       => [
                'name'              => __('publisher', 'vsp-text-domain'),
            ],
        ]);
    }
}
