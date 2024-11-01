<?php


if( !class_exists('era_get_categories') )
{

    /**
     * Return Categories on a "Select <select>", "List <ul>", "select box"
     * @package CTFramework
     * @since Version 1.0.0
     * 
     */
    class era_get_categories {

        /**
         * Return All Post Types Categories on a "Select <select>"
         * @package CTFramework
         * @since Version 1.0.0
         * 
         */
        static function all_cats_by_select( $id = '', $class = '', $name = '', $selected_cat = 0 )
        {
            // get All taxonomies
            $new_taxonomies = get_taxonomies( '' , 'names'  ); 

            // Taxonomies not needed 
            $del_array = array('post_tag','nav_menu','link_category','post_format');
            foreach ( $del_array as $del ) 
            {
                foreach ( $new_taxonomies as $taxy ) 
                {
                    if( $del == $taxy )
                    {
                        unset($new_taxonomies[$taxy]);
                    }
                }
            }

            // Get Categories by taxonomies            
            $html  = '<select id="'.$id.'" class="'.$class.'" name="'.$name.'" >';
            $html .= '<option  value="0" data-category_id="0" >Choose a Category</option>';
            foreach ( $new_taxonomies as $taxy ) 
            {
                $html .= '<optgroup label="'.$taxy.'">';
                $args = array(
                    'hide_empty'    => 0,
                    'hierarchical'  => 1,
                    'echo'          => 0,
                    'taxonomy'      => $taxy,
                    'title_li'      => $taxy,
                    'style'         => 'none',
                    'walker'        => new Era_all_cats_as_select( $selected_cat )
                );
                $html .= wp_list_categories($args);
                $html .= '</optgroup>';
            }
            $html .= '</select>';
            echo $html;
        }


    }// End Class

}


if( !class_exists('Era_all_cats_as_select') )
{
    /**
     * Create HTML '<select>' of categories.
     *
     * @package WordPress
     * @since 2.1.0
     * @uses Walker
     */
    class Era_all_cats_as_select extends Walker_Category {

            public $selected_cat;

            function __construct( $selected_cat = 0 ) {

                $this->selected_cat = $selected_cat;
            }

            /**
             * @see Walker::$tree_type
             * @since 2.1.0
             * @var string
             */
            var $tree_type = 'category';
            /**
             * @see Walker::$db_fields
             * @since 2.1.0
             * @todo Decouple this
             * @var array
             */
            var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
            /**
             * @see Walker::start_lvl()
             * @since 2.1.0
             *
             * @param string $output Passed by reference. Used to append additional content.
             * @param int $depth Depth of category. Used for tab indentation.
             * @param array $args Will only append content if style argument value is 'list'.
             */
            function start_lvl( &$output, $depth = 0, $args = array() ) {
            }
            /**
             * @see Walker::end_lvl()
             * @since 2.1.0
             *
             * @param string $output Passed by reference. Used to append additional content.
             * @param int $depth Depth of category. Used for tab indentation.
             * @param array $args Will only append content if style argument value is 'list'.
             */
            function end_lvl( &$output, $depth = 0, $args = array() ) {
            }
            /**
             * @see Walker::start_el()
             * @since 2.1.0
             *
             * @param string $output Passed by reference. Used to append additional content.
             * @param object $category Category data object.
             * @param int $depth Depth of category in reference to parents.
             * @param array $args
             */
            function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
                    extract($args);

                    // Category Name
                    $cat_name = esc_attr( $category->name );
                    $cat_name = apply_filters( 'list_cats', $cat_name, $category );

                    // Prepare Select "<option>"
                    $indent = '';
                    if ( $category->category_parent != 0 )
                    {
                        $indent = '&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                    $selected = $this->selected_cat == $category->cat_ID ? ' selected="selected" ' : '';
                    $op  = '<option value="'.$category->cat_ID.'" data-taxonomy="'.$category->taxonomy.'" data-category_id="'.$category->cat_ID.'" '.$selected.' >';
                    $op .= $indent.$cat_name . '</option>';
                    
                    // Output
                    $output .= $op;
            }
            /**
             * @see Walker::end_el()
             * @since 2.1.0
             *
             * @param string $output Passed by reference. Used to append additional content.
             * @param object $page Not used.
             * @param int $depth Depth of category. Not used.
             * @param array $args Only uses 'list' for whether should append to output.
             */
            function end_el( &$output, $page, $depth = 0, $args = array() ) {
                    $output .= "";
            }
    }// End Class
}