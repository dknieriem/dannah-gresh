<?php
namespace OTGS\Toolset\Types\Field\Group\Repeatable;

/**
 * Class TreeFactory
 * @package OTGS\Toolset\Types\Field\Group\Repeatable
 *
 * @since 3.0.3
 */
class TreeFactory {

	/** @var \wpdb */
	private $wpdb;

	/** @var \Types_Field_Group_Repeatable_Service  */
	private $service_rfg;

	/**
	 * TreeFactory constructor.
	 *
	 * @param \wpdb $wpdb
	 * @param \Types_Field_Group_Repeatable_Service $service_rfg
	 */
	public function __construct( \wpdb $wpdb, \Types_Field_Group_Repeatable_Service $service_rfg ) {
		$this->wpdb = $wpdb;
		$this->service_rfg = $service_rfg;
	}

	/**
	 * Get the tree of an RFG no matter which position the given $rfg has
	 *
	 * @param \Types_Field_Group_Repeatable $rfg
	 *
	 * @return Tree
	 */
	public function getTreeByRFG( \Types_Field_Group_Repeatable $rfg ) {
		// get a new tree object
		$tree = $this->newTreeObject();

		// add parents to tree
		foreach( $this->loadParents( $rfg ) as $parent_rfg ) {
			$tree->add( $parent_rfg );
		}

		// the given rfg is between it's parents and children
		$tree->add( $rfg );

		// add childrens to tree
		foreach( $this->loadChildren( $rfg ) as $child_rfg ) {
			$tree->add( $child_rfg );
		}

		// return
		return $tree;
	}

	/**
	 * Get the tree of an RFG by RFG Id
	 *
	 * @param $rfg_id
	 *
	 * @return bool|Tree
	 */
	public function getTreeByRFGId( $rfg_id ) {
		if( ! $rfg = $this->service_rfg->get_object_by_id( $rfg_id ) ) {
			// no rfg found by id = no tree
			return false;
		}

		return $this->getTreeByRFG( $rfg );
	}

	/**
	 * @return Tree
	 */
	private function newTreeObject() {
		return new Tree();
	}

	/**
	 * @param \Types_Field_Group_Repeatable $rfg
	 * @param array $parents
	 *
	 * @return array
	 */
	private function loadParents( \Types_Field_Group_Repeatable $rfg, $parents = array() ) {
		$parent_post_id = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT `post_id` 
				 FROM {$this->wpdb->postmeta}
				 WHERE `meta_value` LIKE %s
				 LIMIT 1",
				'%' . $rfg->get_id_with_prefix() . '%' // as we're searching in a string of fields we need wildcards
			)
		);

		if( $parent_post_id && $parent_rfg = $this->service_rfg->get_object_by_id( $parent_post_id ) ) {
			$parents[ $parent_rfg->get_id() ] = $parent_rfg;

			// go further up in the tree
			return $this->loadParents( $parent_rfg, $parents );
		}

		// no more parents, return the collected in the reversed order
		// "array_reverse" because we collect them from bottom (nested) to top level
		// but we want the tree to be from top to bottom
		return array_reverse( $parents, $keep_keys = true );
	}

	/**
	 * @param \Types_Field_Group_Repeatable $rfg
	 * @param array $children
	 *
	 * @return array
	 */
	private function loadChildren( \Types_Field_Group_Repeatable $rfg, $children = array() ) {
		foreach( $rfg->get_field_slugs() as $field_slug ) {
			if( $child_rfg = $this->service_rfg->get_object_from_prefixed_string( $field_slug ) ) {
				$children[ $child_rfg->get_id() ] = $child_rfg;

				$children = $this->loadChildren( $child_rfg, $children );
			}
		}

		return $children;
	}
}