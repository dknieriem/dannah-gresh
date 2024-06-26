<?php

namespace OTGS\Toolset\Types\Controller\Compatibility;

use OTGS\Toolset\Common\Utils\Condition\Plugin\Gutenberg\IsUsedForPost;
use OTGS\Toolset\Types\Compatibility\Gutenberg\View\PostEdit as GutenbergPostEdit;
use OTGS\Toolset\Types\Page\Extension\EditPost\PerPostEditorMode;

/**
 * Class Gutenberg
 *
 * @package OTGS\Toolset\Types\Controller\Compatibility
 *
 * @since 3.2
 */
class Gutenberg {


	/** @var \Toolset_Post_Type_Repository */
	private $post_type_repository;


	/** @var \Toolset_Element_Factory */
	private $element_factory;


	/** @var IsUsedForPost */
	private $is_used_for_post_condition;


	/**
	 * Gutenberg constructor.
	 *
	 * @param \Toolset_Post_Type_Repository $post_type_repository
	 * @param \Toolset_Element_Factory $element_factory
	 * @param IsUsedForPost $is_used_for_post_condition
	 */
	public function __construct(
		\Toolset_Post_Type_Repository $post_type_repository,
		\Toolset_Element_Factory $element_factory,
		IsUsedForPost $is_used_for_post_condition
	) {
		$this->post_type_repository = $post_type_repository;
		$this->element_factory = $element_factory;
		$this->is_used_for_post_condition = $is_used_for_post_condition;
	}


	/**
	 * Initialize the compatibility with the Gutenberg editor.
	 *
	 * @since 3.2.2
	 */
	public function initialize() {

		$that = $this; // PHP 5.3 compatibility.

		// Handle the actions related to the "per post" editor mode.
		add_action( 'save_post', function (
			/** @noinspection PhpUnusedParameterInspection */
			$post_id, $post, $is_update
		) use (
			$that
		) {
			if ( ! $that->is_active_for_current_post_type() ) {
				// No Gutenberg for this post type at all - nothing to do here.
				return;
			}

			$per_post_editor_mode = new PerPostEditorMode( $that->post_type_repository, $that->element_factory, $that->is_used_for_post_condition );
			$per_post_editor_mode->on_save_post( $post, $is_update );
		}, 10, 3 );

		// Support both core implementation and the Gutenberg plugin.
		foreach( array( 'use_block_editor_for_post', 'gutenberg_can_edit_post' ) as $filter_name ) {
			add_filter( $filter_name, function ( $use_block_editor, $post ) use ( $that ) {
				$per_post_editor_mode = new PerPostEditorMode( $that->post_type_repository, $that->element_factory, $that->is_used_for_post_condition );

				return $per_post_editor_mode->use_block_editor_for_post( $use_block_editor, $post );
			}, 10, 2 );
		}

		// Note: needs to happen after the added filters, especially after the use_block_editor_for_post one.
		global $pagenow;
		if( 'post.php' === $pagenow && 'edit' === toolset_getget( 'action' ) && $this->is_active_for_current_post_type() ) {
			$per_post_editor_mode = new PerPostEditorMode( $this->post_type_repository, $this->element_factory, $this->is_used_for_post_condition );
			$per_post_editor_mode->on_edit_post();
		}
	}


	/**
	 * @return bool
	 */
	public function is_active_for_current_post_type() {
		if ( ! $current_screen = get_current_screen() ) {
			// called to early
			return false;
		}

		// Check Gutenberg
		if ( function_exists( 'use_block_editor_for_post_type' ) ) {
			// >= WP 5.0
			if ( ! use_block_editor_for_post_type( $current_screen->post_type ) ) {
				// no block editor active for this post type
				return false;
			}
		} else {
			// < WP 5.0
			if ( ! function_exists( 'gutenberg_can_edit_post_type' )
				 || ! gutenberg_can_edit_post_type( $current_screen->post_type ) ) {
				// no gutenberg at all or not active for this post type
				return false;
			}
		}

		// gutenberg active
		return true;
	}

	/**
	 * Load Gutenberg compatibility on post edit screen
	 *
	 * @hook load-post.php (Post Edit Page)
	 *
	 * @param GutenbergPostEdit $view
	 */
	public function post_edit_screen( GutenbergPostEdit $view ) {
		// hook frontend scripts loading to admin_enqueue_scripts
		add_action( 'admin_enqueue_scripts', function () use ( $view ) {
			// gutenberg active for the current post type
			$view->enqueueScripts();
		}, 11 );
	}

}
