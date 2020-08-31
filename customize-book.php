//start books custom type
$args = array(
'label' => __('Books'),
'singular_label' => __('Book'),
'public' => true,
'show_ui' => true,
'capability_type' => 'page',
'hierarchical' => false,
'rewrite' => true,
'query_var' => 'books',
'supports' => array('title', 'thumbnail')
);
register_post_type( 'book' , $args );

//end books custom type

//start books meta boxes

/*
original code
function book_box_meta_box() {

    $screens = array( 'post', 'page', 'book' );

    foreach ( $screens as $screen ) {
        add_meta_box(
            'book-box',
            __( 'Book Box', 'sinatra' ),
            'book_box_meta_box_callback',
            $screen
        );
    }
}

add_action( 'add_meta_boxes', 'book_box_meta_box' );
*/

add_action( 'admin_menu', 'book_box_meta_box' );
 
function book_box_meta_box() {
 
	add_meta_box(
		'book-box', // metabox ID
		'Book Box', // title
		'book_box_meta_box_callback', // callback function
		'book', // post type or post types in array ex. page, post or custom post type like author, portfolio
		'normal', // position (normal, side, advanced)
		'default' // priority (default, low, high, core)
	);
 
}

function book_cpt() {

    $args = array(
        'label'                => 'Books',
        'public'               => true,
        'register_meta_box_cb' => 'book_box_meta_box'
    );

    register_post_type( 'book', $args );
}

add_action( 'init', 'book_cpt' );

function book_box_meta_box_callback( $post ) {

    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'book_box_nonce', 'book_box_nonce' );

    $book_author = get_post_meta( $post->ID, 'book_author', true );
	$book_type = get_post_meta( $post->ID, 'book_type', true );
  
	echo '<table class="form-table">
		<tbody>
			<tr>
				<th><label for="book_author">Book Author</label></th>
				<td><input type="text" id="book_author" name="book_author" value="' . esc_attr( $book_author ) . '" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="book_type">Book Type</label></th>
				<td>
					<select id="book_type" name="book_type">
						<option value="">Select...</option>
						<option value="Hard Bound"' . selected( 'Hard Bound', $book_type, false ) . '>Hard Bound</option>
						<option value="Soft Bound"' . selected( 'Soft Bound', $book_type, false ) . '>Soft Bound</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>';
}


/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id
 */
function save_book_box_meta_box_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['book_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['book_box_nonce'], 'book_box_nonce' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }

    }
    else {

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */

    	if( isset( $_POST[ 'book_author' ] ) ) {
		update_post_meta( $post_id, 'book_author', sanitize_text_field( $_POST[ 'book_author' ] ) );
	} else {
		delete_post_meta( $post_id, 'book_author' );
	}
	if( isset( $_POST[ 'book_type' ] ) ) {
		update_post_meta( $post_id, 'book_type', sanitize_text_field( $_POST[ 'book_type' ] ) );
	} else {
		delete_post_meta( $post_id, 'book_type' );
	}
	
	
}

add_action( 'save_post', 'save_book_box_meta_box_data' );

//end books meta boxes
