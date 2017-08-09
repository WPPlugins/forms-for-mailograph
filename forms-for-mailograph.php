<?php
/*
Plugin Name: Forms for Mailograph
Plugin URI: http://www.aytech.ca/wordpress-plugins/forms-for-mailograph.zip
Description: Forms for Mailograph email marketing.
Version: 0.2
Author: AYTechnologies Agency
Author URI: http://www.aytech.ca
License: GPLv2 or later.
*/

/**
 * Adds Mailograph Forms widget.
 */
class Forms_for_Mailograph extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Forms_for_Mailograph', // Base ID
			__( 'Forms for Mailograph', 'mailograph.com' ), // Name
			array( 'description' => __( 'Forms for Mailograph email marketing: www.mailograph.com', 'mailograph.com' ), ) // Args
		);
	}	

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		?>
		<div class="mailograph-forms">
		    <form>
		        <div>
		        	<?php if ($instance[ 'name' ]=='on') { ?>
		        	<label for="name">Name</label><br/>
		            <input type="text" name="name" placeholder="Name"/>
		            <?php } ?>
		            <label for="email">Email</label><br/>
		            <input type="text" name="email" placeholder="Email Address"/>
		            <button type="button" name="submit" class="btn">Subscribe</button>
		        </div>
		    </form>
		</div>
		<script>
			var validateEmail = function(email){
                var filter = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
                isValid= String(email).search (filter) != -1;	

                if(!isValid){
                    alert('Please enter a valid email address');
                }
                return isValid;
            };

		    jQuery(document).ready(function(){
		        jQuery('.mailograph-forms form button[name=submit]').click(function ()
		        {

		            var form = jQuery('.mailograph-forms form');
		            var email = form.find('input[name=email]').val();
		        	if(!validateEmail(email)){
		        		return;
		        	}
		            <?php if ($instance[ 'name' ]=='on') { ?>
		            var name = form.find('input[name=name]').val();
	            	<?php } ?>

		            var url = 'email=' + email ;
		            <?php if ($instance[ 'name' ]=='on') { ?>
		            var url = url + '&name=' + name;
		            <?php } ?>
		            var url = url + '&list=<?php echo $instance[ 'list_id' ]?>&boolean=true';
		            jQuery(".mailograph-forms").fadeOut('500');
		            jQuery.ajax({
		                url: '<?php echo plugin_dir_url(__FILE__); ?>subscribe.php',
		                data: url,
		                type: "POST",
		                success: function (response)
		                {
		                    jQuery(".mailograph-forms").html(response).fadeIn('1000');
		                }
		            });
		            return false; // Don't actually submit the form
		        });
		    });

		</script>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'api_key' ); ?>"><?php _e( 'API Key:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ); ?>" type="text" value="<?php echo esc_attr( $instance['api_key'] ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'list_id' ); ?>"><?php _e( 'List Id:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'list_id' ); ?>" name="<?php echo $this->get_field_name( 'list_id' ); ?>" type="text" value="<?php echo esc_attr( $instance['list_id'] ); ?>">
		</p>
		<label><?php _e( 'Fields:' ); ?></label> <br/>
		<input class="checkbox" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" type="checkbox"  <?php echo ($instance[ 'name' ]=='on')?'checked="checked"':'' ; ?>  /> <label><?php _e( 'Name' ); ?></label>
		<br/>
		<input class="checkbox" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" type="checkbox" onclick="return false" checked /> <label><?php _e( 'Email - mandatory' ); ?></label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Theme'); ?>:
		<select id="<?php echo $this->get_field_id('theme'); ?>" name="<?php echo $this->get_field_name('theme'); ?>" style="width:140px;" >
			<option value="default" <?php echo ($meta_format === 'default' ? ' selected="selected"' : ''); ?>><?php _e('Default')?></option>
			<!--<option value="icons" <?php echo ($meta_format === 'icons' ? ' selected="selected"' : ''); ?>><?php _e('Icons'); ?></option>
			<option value="labels" <?php echo ($meta_format === 'labels' ? ' selected="selected"' : ''); ?>><?php _e('Labels'); ?></option> -->
		</select>
		</label>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['api_key'] = strip_tags( $new_instance['api_key'] );
        $instance['list_id'] = strip_tags( $new_instance['list_id'] );
        $instance['name'] = strip_tags( $new_instance['name'] );
		$instance['email'] = strip_tags( $new_instance['email'] );
		$instance['theme'] = strip_tags( $new_instance['theme'] );

		return $instance;
	}
}

add_action( 'widgets_init', 'register_mailogram_form_widget' );

// register Foo_Widget widget
function register_mailogram_form_widget() {
    register_widget( 'Forms_for_Mailograph' );
}

?>