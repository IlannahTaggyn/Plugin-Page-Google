<?php

/**
 * Page Google
 *
 * Plugin Name: Page Google
 * Plugin URI: https://github.com/IlannahTaggyn
 * Description: Um simples plugin para criar um simples custom post type para postar conteúdos de melhor engajamento no Google.
 * Version: 2.0.1
 * Author: Ilannah Taggyn
 * License: GPLv2 or later
 * Text Domain: page-google
 *
 * Este plugin é gratuito e é um simples plugin para criar um simples custom post type para postar conteúdos de melhor engajamento no Google.
 *
 * Na versão 2.0.1 foi incluindo o registro das taxonomias personalizadas "page_google_category" e "page_google_tag" .
 */

declare(strict_types=1); // Declaração de tipos estritos

if (!defined('ABSPATH')) {
  die('Invalid request.');
}

class PageGoogle
{
  public function __construct()
  {
    add_action('init', [$this, 'create_custom_post_type_modulo']);
    add_action('init', [$this, 'create_custom_taxonomies']); // Adicionando ação para registrar as taxonomias personalizadas
  }

  public function create_custom_post_type_modulo(): void
  {
    $labels = [
      'name' => _x('Page Google', 'page-google', 'text_domain'),
      'singular_name' => _x('Page Google', 'page-google', 'text_domain'),
      'menu_name' => __('Page Google', 'text_domain'),
      'name_admin_bar' => __('Page Google', 'text_domain'),
    ];

    $args = [
      'label' => __('Page Google', 'text_domain'),
      'description' => __('Descrição da Page Google', 'text_domain'),
      'labels' => $labels,
      'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes'], // Adicionado 'page-attributes'
      'taxonomies' => ['page_google_category', 'page_google_tag'], // Alterado para as taxonomias personalizadas
      'hierarchical' => true, // Definido como 'true' para suportar hierarquia de páginas
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_position' => 3,
      'show_in_admin_bar' => true,
      'show_in_nav_menus' => true,
      'can_export' => true,
      'menu_icon' => 'dashicons-edit-page',
      'has_archive' => true,
      'exclude_from_search' => false,
      'publicly_queryable' => true,
      'capability_type' => 'page',
      'template' => [
        [
          'core/paragraph',
          [
            'placeholder' => 'Add description...'
          ]
        ]
      ],
      'template_lock' => 'all', // Define o modelo padrão como bloqueado
    ];

    register_post_type('page_google', $args);
  }

  public function create_custom_taxonomies(): void
  {
    // Registrando uma taxonomia personalizada para categorias
    register_taxonomy(
      'page_google_category',
      'page_google',
      array(
        'label' => __('Categorias', 'page-google'),
        'rewrite' => array('slug' => 'page-google-category'),
        'hierarchical' => true,
      )
    );

    // Registrando uma taxonomia personalizada para tags
    register_taxonomy(
      'page_google_tag',
      'page_google',
      array(
        'label' => __('Tags', 'page-google'),
        'rewrite' => array('slug' => 'page-google-tag'),
        'hierarchical' => false,
      )
    );
  }

  public function activate(): void
  {
    $this->create_custom_post_type_modulo();
    $this->create_custom_taxonomies();

    flush_rewrite_rules();

    global $wpdb;
    $wpdb->get_results("INSERT INTO wp_posts (post_author, post_content, post_title, post_status, comment_status, ping_status, post_type, comment_count) VALUES (1, 'Teste Pagina Google', 'Teste Pagina Google', 'publish', 'open', 'open', 'modulo', 0);");
  }

  public function deactivate(): void
  {
    flush_rewrite_rules();
  }

  public function uninstall(): void
  {
    flush_rewrite_rules();

    global $wpdb;
    $wpdb->get_results("DELETE FROM wp_posts WHERE post_type='page_google';");
  }
}

if (class_exists('PageGoogle')) {
  $page_google = new PageGoogle();
  register_activation_hook(__FILE__, [$page_google, 'activate']);
  register_deactivation_hook(__FILE__, [$page_google, 'deactivate']);
  register_uninstall_hook(__FILE__, [$page_google, 'uninstall']);
}
