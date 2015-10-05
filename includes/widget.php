<?php
/**
 * Widget
 *
 * @package     WPTallyConnect\Widget
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Tally Widget
 *
 * @since       1.0.0
 */
class wptallyconnect_widget extends WP_Widget {

    /**
     * Constructor
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function wptallyconnect_widget() {
        parent::WP_Widget(
            false,
            __( 'WP Tally', 'wp-tally-connect' ),
            array(
                'description'  => __( 'Display a tally of your WordPress plugin downloads.', 'wp-tally-connect' )
            )
        );
    }

    /**
     * Widget definition
     *
     * @access      public
     * @since       1.0.0
     * @see         WP_Widget::widget
     * @param       array $args Arguments to pass to the widget
     * @param       array $instance A given widget instance
     * @return      void
     */
    public function widget( $args, $instance ) {
        if( ! isset( $args['id'] ) ) {
            $args['id'] = 'wp_tally_widget';
        }

        $title = apply_filters( 'widget_title', $instance['title'], $instance, $args['id'] );

        echo $args['before_widget'];

        if( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        do_action( 'wp_tally_before_widget' );

        if( $instance['username'] ) {
            $data = wptallyconnect_get_data( $instance['username'] );
            if( array_key_exists( 'error', $data['plugins'] ) ) {
                echo $data['plugins']['error'];
            } else {
				// Do stuff
				$i = 1;
				foreach( $data['plugins'] as $plugin) {
					echo "<div class='tally-plugin-meta'>";
					echo "<a class='tally-plugin-link' href='". $plugin['url'] ."' target='_blank'>". $plugin['name'] ."</a><br />";
					// Display the version if user checks it in the widget
					if('on' == $instance['version'] ) {
						echo "<span class='tally-plugin-meta-item'><strong>Version: </strong>". $plugin['version'] ."</span><br />";
					}
					// Display the date added if user checks it in the widget
					if('on' == $instance['added'] ) {
						echo "<span class='tally-plugin-meta-item'><strong>Added: </strong>". $plugin['added'] ."<br /></span>";
					}
					// Display the last updated date if user checks it in the widget
					if('on' == $instance['updated'] ) {
						echo "<span class='tally-plugin-meta-item'><strong>Updated: </strong>". $plugin['updated'] ."<br /></span>";
					}
					// Display the rating if user checks it in the widget
					if('on' == $instance['ratings'] ) {
						echo "<span class='tally-plugin-meta-item'><strong>Rating: </strong>" . ( empty( $plugin['rating'] ) ? "not yet rated<br />" : $plugin['rating'] . " out of 5 stars<br /></span>" );
					}
					// Display the downloads if user checks it in the widget
					if('on' == $instance['downloads'] ) {
						echo "<span class='tally-plugin-meta-item'><strong>Downloads: </strong>". number_format( $plugin['downloads'] ) ."</span><br />";
					}
					echo "</div>";
					if ($i++ == $instance['limit']) break;
				}
				// Display link to profile if user checks it in the widget
				if('on' == $instance['viewall'] ) {
					echo "<a class='tally-profile-link' href='https://profiles.wordpress.org/". $instance['username'] ."' target='_blank'>View all plugins &rarr;</a>";
				}
            }
        } else {
            _e( 'No username has been specified!', 'wp-tally-connect' );
        }

        do_action( 'wp_tally_after_widget' );
        
        echo $args['after_widget'];
    }


    /**
     * Update widget options
     *
     * @access      public
     * @since       1.0.0
     * @see         WP_Widget::update
     * @param       array $new_instance The updated options
     * @param       array $old_instance The old options
     * @return      array $instance The updated instance options
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']      = strip_tags( $new_instance['title'] );
        $instance['username']   = strip_tags( $new_instance['username'] );
        $instance['limit']   	= strip_tags( $new_instance['limit'] );
        $instance['version']   	= $new_instance['version'];
        $instance['added']   	= $new_instance['added'];
        $instance['updated']   	= $new_instance['updated'];
        $instance['ratings']   	= $new_instance['ratings'];
        $instance['downloads']  = $new_instance['downloads'];
        $instance['viewall']  = $new_instance['viewall'];
        $instance['style']      = $new_instance['style'];

        return $instance;
    }


    /**
     * Display widget form on dashboard
     *
     * @access      public
     * @since       1.0.0
     * @see         WP_Widget::form
     * @param       array $instance A given widget instance
     * @return      void
     */
    public function form( $instance ) {
        $defaults = array(
            'title'     => '',
            'username'  => '',
            'limit'  => '',
            'version'  => '',
            'added'  => '',
            'updated'  => '',
            'ratings'  => '',
            'downloads'  => '',
            'viewall'  => '',
            'style'     => 'standard'
        );

        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'wp-tally-connect' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>
		
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php _e( 'WordPress.org Username:', 'wp-tally-connect' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo $instance['username']; ?>" />
        </p>
		
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php _e( 'Amount of plugins to show:', 'wp-tally-connect' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" type="number" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" min="1" max="999" value="<?php echo $instance['limit']; ?>" />
        </p>
		
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php _e( 'Widget Style:', 'wp-tally-connect' ); ?></label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
                <option value="standard" <?php selected( $instance['style'], 'standard' ); ?>><?php _e( 'Standard', 'wp-tally-connect' ); ?></option>
                <option value="goal" <?php selected( $instance['style'], 'goal' ); ?>><?php _e( 'Goal', 'wp-tally-connect' ); ?></option>
                <option value="countup" <?php selected( $instance['style'], 'countup' ); ?>><?php _e( 'Count Up', 'wp-tally-connect' ); ?></option>
            </select>
        </p>
		
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['version'], 'on'); ?> id="<?php echo $this->get_field_id('version'); ?>" name="<?php echo $this->get_field_name('version'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'version' ) ); ?>"><?php _e( 'Display version?', 'wp-tally-connect' ); ?></label>
        </p>
		
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['added'], 'on'); ?> id="<?php echo $this->get_field_id('added'); ?>" name="<?php echo $this->get_field_name('added'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'added' ) ); ?>"><?php _e( 'Display date added?', 'wp-tally-connect' ); ?></label>
        </p>
		
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['updated'], 'on'); ?> id="<?php echo $this->get_field_id('updated'); ?>" name="<?php echo $this->get_field_name('updated'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'updated' ) ); ?>"><?php _e( 'Display last updated?', 'wp-tally-connect' ); ?></label>
        </p>
		
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['ratings'], 'on'); ?> id="<?php echo $this->get_field_id('ratings'); ?>" name="<?php echo $this->get_field_name('ratings'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'ratings' ) ); ?>"><?php _e( 'Display ratings?', 'wp-tally-connect' ); ?></label>
        </p>
		
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['downloads'], 'on'); ?> id="<?php echo $this->get_field_id('downloads'); ?>" name="<?php echo $this->get_field_name('downloads'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'downloads' ) ); ?>"><?php _e( 'Display download count?', 'wp-tally-connect' ); ?></label>
        </p>
		
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['viewall'], 'on'); ?> id="<?php echo $this->get_field_id('viewall'); ?>" name="<?php echo $this->get_field_name('viewall'); ?>" /> 
			<label for="<?php echo esc_attr( $this->get_field_id( 'viewall' ) ); ?>"><?php _e( 'Display link to WordPress.org profile?', 'wp-tally-connect' ); ?></label>
        </p>
        <?php
    }
}


/**
 * Register the new widget
 *
 * @since       1.0.0
 * @return      void
 */
function wptallyconnect_register_widget() {
    register_widget( 'wptallyconnect_widget' );
}
add_action( 'widgets_init', 'wptallyconnect_register_widget' );
