<?php

class acf_field_page_children extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {

		/* Vars */
		$this->name     = 'page_children';
		$this->label    = __('Page Children', 'acf-page_children');
		$this->category = 'relational';



		/* Defaults */
		$this->defaults = array(
			'multiple'      => 1,
			'allow_null'    => 0,
			'choices'       => array(),
			'return_format' => 'object',
			'max'           => 0
		);


		// do not delete!
    	parent::__construct();
    	
	}
	






	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function render_field( $field ) {

		$fieldHTML = '';
		$page_type = get_post_type();
		$els       = array();
		$choices   = array();
		$atts      = array(
			'id'       => $field['id'],
			'class'    => "acf-page_children {$field['class']}",
			'data-max' => $field['max'],
			'multiple' => 'multiple',
			'name'     => $field['name'] . '[]'
		);


		/* convert value to array */
		$field['value'] = acf_force_type_array($field['value']);


		/* store full name in hidden input for fuck knows what reason */
		acf_hidden_input(array(
			'type'	=> 'hidden',
			'name'	=> $field['name'],
		));


		/* generate list of all children */
		$children = get_pages(array(
			'sort_column' => 'menu_order',
			'child_of'    => get_the_ID(),
			'post_type'   => $page_type,
			'post_status' => 'publish'
		));
		foreach($children as $child){
			$field['choices'][$child->ID] = $child->post_title;
		}


		/* loop through values and add them as options */
		if( !empty($field['choices']) ) {
			foreach( $field['choices'] as $k => $v ) {

					$els[] = array(
						'type'     => 'option',
						'value'    => $k,
						'label'    => $v,
						'selected' => in_array($k, $field['value'])
					);

					$choices[] = $k;

			}
		}


		/* Construct HTML */
		if( !empty($els) ) {

			$fieldHTML = '<select ' . acf_esc_attr( $atts ) . '>';

			foreach( $els as $el ) {

					// validate selected
					if( acf_extract_var($el, 'selected') ) {
						$el['selected'] = 'selected';
					}

					$fieldHTML.= '<option ' . acf_esc_attr( $el ) . '>' . acf_extract_var($el, 'label') . '</option>';

			}

			$fieldHTML.= '</select>';



			/* apply max items filtering if required */
			if($field['max']>0){
				$fieldHTML.= <<<EOD
				<script type="text/javascript">
					(function($){

						var _max         = {$field['max']},
                _selectEl    = $('#{$field['id']}'),
                _selectVal   = _selectEl.val();

					  _selectEl.on('change', function(){
						  if($(this).val() && $(this).val().length > _max){
							  $(this).val(_selectVal);
						  }

						  _selectVal   = _selectEl.val();
					  })

					})(jQuery);
				</script>
EOD;
			}
			
		}


		echo $fieldHTML;
	}








	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function render_field_settings( $field ) {


		// max
		if( $field['max'] < 1 ) {
			$field['max'] = 0;
		}


		acf_render_field_setting( $field, array(
			'label'        => __('Maximum Children', 'acf'),
			'type'         => 'number',
			'name'         => 'max',
			'instructions' => 'set to "0" for no limit',
		));


		// return_format
		acf_render_field_setting( $field, array(
			'label'   => __('Return Format', 'acf'),
			'type'    => 'radio',
			'name'    => 'return_format',
			'choices' => array(
				'object'  => __("Post Object", 'acf'),
				'id'      => __("Post ID", 'acf'),
			),
			'layout'  => 'horizontal',
		));


	}







/*
* format_value()
*
* This filter is applied to the $value after it is loaded from the db and before it is returned to the template
*
* @type filter
* @since 3.6
* @date 23/01/13
*
* @param $value (mixed) the value which was loaded from the database
* @param $post_id (mixed) the $post_id from which the value was loaded
* @param $field (array) the field array holding all the field options
*
* @return $value (mixed) the modified value
*/

	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) {
			return $value;
		}

		//return full page objects if necessary
		if( $field['return_format'] === 'object' ) {
			$query = new WP_Query( array(
				'post_type' => get_post_type($post_id),
				'post__in'  => $value,
				'nopaging'  => true
			) );

			$value = $query->posts;

			unset($query);
		}

		return $value;
	}

}







// create field
new acf_field_page_children();

?>
