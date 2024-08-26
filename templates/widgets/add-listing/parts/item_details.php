<?php
$_id = apply_filters( 'stm_listings_input', null, 'item_id' );

$data = apply_filters( 'stm_get_single_car_listings', array() );

$terms_args = array(
	'orderby'    => 'name',
	'order'      => 'ASC',
	'hide_empty' => false,
	'fields'     => 'all',
	'pad_counts' => true,
);

if ( $custom_listing_type && $listing_types_options ) {
	$_taxonomy        = ( $listing_types_options[ $custom_listing_type . '_addl_required_fields' ] ) ? $listing_types_options[ $custom_listing_type . '_addl_required_fields' ] : array();
	$number_as_input  = ( $listing_types_options[ $custom_listing_type . '_addl_number_as_input' ] ) ? $listing_types_options[ $custom_listing_type . '_addl_number_as_input' ] : '';
	$history_report   = ( $listing_types_options[ $custom_listing_type . '_addl_history_report' ] ) ? $listing_types_options[ $custom_listing_type . '_addl_history_report' ] : '';
	$details_location = ( $listing_types_options[ $custom_listing_type . '_addl_details_location' ] ) ? $listing_types_options[ $custom_listing_type . '_addl_details_location' ] : false;
	$required_fields  = multilisting_get_main_taxonomies_to_fill( $custom_listing_type );
} else {
	$_taxonomy        = apply_filters( 'stm_me_get_nuxy_mod', array(), 'addl_required_fields' );
	$number_as_input  = apply_filters( 'stm_me_get_nuxy_mod', '', 'addl_number_as_input' );
	$history_report   = apply_filters( 'stm_me_get_nuxy_mod', '', 'addl_history_report' );
	$details_location = apply_filters( 'stm_me_get_nuxy_mod', false, 'addl_details_location' );
}

$_taxonomy = ( ! $_taxonomy ) ? array() : $_taxonomy;

?>
<div class="stm_add_car_form_1">
	<div class="stm-car-listing-data-single stm-border-top-unit ">
		<div class="title heading-font"><?php esc_html_e( 'Listing Item Details', 'motors-elementor-widgets' ); ?></div>
	</div>

	<?php if ( ! empty( $_taxonomy ) ) : ?>
		<div class="stm-form1-intro-unit">
			<div class="row">
				<?php
				foreach ( $_taxonomy as $_tax ) :
					$tax_info = apply_filters( 'stm_vl_get_all_by_slug', array(), $_tax );

					$terms = array();

					if ( empty( $tax_info['listing_taxonomy_parent'] ) ) {
						$terms = apply_filters( 'stm_get_category_by_slug_all', array(), $_tax, true );
					}

					$has_selected = '';

					if ( ! empty( $_id ) ) {
						$post_terms = wp_get_post_terms( $_id, $_tax );
						if ( ! empty( $post_terms[0] ) ) {
							$has_selected = $post_terms[0]->slug;
						} elseif ( ! empty( $tax_info['slug'] ) ) {
							$has_selected = get_post_meta( $_id, $tax_info['slug'], true );
						}
					}

					$number_field = false;

					if ( $number_as_input && ! empty( $tax_info['numeric'] ) && $tax_info['numeric'] ) {
						$number_field = true;
					}

					if ( $custom_listing_type ) {
						$tax_name = $required_fields[ $_tax ];
					} else {
						$tax_name = stm_get_name_by_slug( $_tax );
					}
					?>
					<div class="col-md-3 col-sm-3 stm-form-1-selects">
						<div class="stm-label heading-font"><?php echo esc_html( $tax_name ); ?>*
						</div>
						<?php if ( $number_field ) : ?>
							<?php $value = get_post_meta( $_id, $tax_info['slug'], true ); ?>
							<input value="<?php echo esc_attr( $value ); ?>" min="0" type="number" name="stm_f_s[<?php echo esc_attr( $_tax ); ?>]" required/>
						<?php else : ?>
							<select class="add_a_car-select" data-class="stm_select_overflowed"
									data-selected="<?php echo esc_attr( $has_selected ); ?>"
									name="stm_f_s[<?php echo esc_attr( str_replace( '-', '_pre_', $_tax ) ); ?>]"
									required="required"
							>
								<option value=""
										selected="selected"><?php esc_html_e( 'Select', 'motors-elementor-widgets' ); ?> <?php echo esc_html( $tax_name ); ?></option>
								<?php
								if ( ! empty( $terms ) ) :
									foreach ( $terms as $_term ) :
										?>
										<option value="<?php echo esc_attr( $_term->slug ); ?>"
											<?php
											if ( ! empty( $has_selected ) && $_term->slug === $has_selected ) {
												echo 'selected';
											}
											?>
										>
											<?php echo esc_html( trim( $_term->name ) ); ?>
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php // phpcs:disable ?>
		<style type="text/css">
			<?php
			foreach( $_taxonomy as $_tax ):
				if ( $custom_listing_type ) {
					$tax_name = $required_fields[ $_tax ];
				} else {
					$tax_name = stm_get_name_by_slug( $_tax );
				}
				?>

			.stm-form1-intro-unit .select2-selection__rendered[title="<?php esc_html_e('Select', 'motors-elementor-widgets'); ?> <?php echo esc_html( apply_filters( 'stm_dynamic_string_translation', $tax_name, 'Add A Car Step 1 Slug Name' ) ); ?>"] {
				background-color: transparent !important;
				border: 1px solid rgba(255, 255, 255, 0.5);
				color: #fff !important;
			}

			.stm-form1-intro-unit .select2-selection__rendered[title="<?php esc_html_e('Select', 'motors-elementor-widgets'); ?> <?php echo esc_html( apply_filters( 'stm_dynamic_string_translation', $tax_name, 'Add A Car Step 1 Slug Name' ) ); ?>"] + .select2-selection__arrow b {
				color: rgba(255, 255, 255, 0.5);
			}

			<?php endforeach; ?>
		</style>
		<?php // phpcs:enable ?>
	<?php endif; ?>

	<div class="stm-form-1-end-unit clearfix">
		<?php if ( ! empty( $data ) && is_array( $_taxonomy ) ) : ?>
			<?php foreach ( $data as $data_key => $data_unit ) : ?>
				<?php
				if ( ! in_array( $data_unit['slug'], $_taxonomy, true ) ) :
					$tax_info = apply_filters( 'stm_vl_get_all_by_slug', array(), $data_unit['slug'] );

					$terms = array();
					if ( empty( $tax_info['listing_taxonomy_parent'] ) ) {
						$terms = get_terms( $data_unit['slug'], $terms_args );
					}

					$is_required = ( isset( $data_unit['required_filed'] ) && $data_unit['required_filed'] ) ? 'required' : '';
					?>
					<div class="stm-form-1-quarter">
						<?php
						if ( ! empty( $data_unit['numeric'] ) && $data_unit['numeric'] ) :
							$value = '';
							if ( ! empty( $_id ) ) {
								$value = get_post_meta( $_id, $data_unit['slug'], true );
							}
							?>

							<input
									type="number"
									class="form-control <?php echo ( ! empty( $value ) ) ? 'stm_has_value' : ''; ?>"
									name="stm_s_s_<?php echo esc_attr( $data_unit['slug'] ); ?>"
									value="<?php echo esc_attr( $value ); ?>"
									placeholder="<?php printf( esc_attr__( 'Enter %1$s %2$s', 'motors-elementor-widgets' ), esc_attr__( $data_unit['single_name'], 'motors-elementor-widgets' ), ( ! empty( $data_unit['number_field_affix'] ) ) ? '(' . esc_attr__( $data_unit['number_field_affix'], 'motors-elementor-widgets' ) . ')' : '' ); ?>"<?php //phpcs:ignore?>
									<?php echo esc_attr( $is_required ); ?>
							/>
						<?php else : ?>
							<select name="stm_s_s_<?php echo esc_attr( $data_unit['slug'] ); ?>"
									class="add_a_car-select" <?php echo esc_attr( $is_required ); ?>>
								<?php
								$selected = '';
								if ( ! empty( $_id ) ) {
									$selected = get_post_meta( $_id, $data_unit['slug'], true );
								}
								?>
								<option value=""><?php printf( esc_html__( 'Select %s', 'motors-elementor-widgets' ), esc_html__( $data_unit['single_name'], 'motors-elementor-widgets' ) ) ?></option><?php //phpcs:ignore?>
								<?php
								if ( ! empty( $terms ) ) :
									foreach ( $terms as $_term ) :
										?>
										<?php
										$selected_opt = '';
										if ( $selected === $_term->slug ) {
											$selected_opt = 'selected';
										}
										?>
										<option value="<?php echo esc_attr( $_term->slug ); ?>" <?php echo esc_attr( $selected_opt ); ?>><?php echo esc_attr( $_term->name ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						<?php endif; ?>
						<div class="stm-label">
							<?php if ( ! empty( $data_unit['font'] ) ) : ?>
								<i class="<?php echo esc_attr( $data_unit['font'] ); ?>"></i>
							<?php endif; ?>
							<?php echo esc_html( apply_filters( 'stm_dynamic_string_translation', $data_unit['single_name'], 'Add A Car Step 1 Taxonomy Label ' . $data_unit['single_name'] ) ); ?>
							<?php
							if ( isset( $data_unit['required_filed'] ) && $data_unit['required_filed'] ) {
								echo '*';
							}
							?>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>

			<style type="text/css">
				<?php
				foreach ( $data as $data_unit ) :
					?>

				.stm-form-1-end-unit .select2-selection__rendered[title="<?php echo esc_attr__( 'Select', 'motors-elementor-widgets' ); ?> <?php echo esc_html( apply_filters( 'stm_dynamic_string_translation', $data_unit['single_name'], 'Add A Car Step 1 Taxonomy Label' ) ); ?>"] {
					background-color: transparent !important;
					border: 1px solid rgba(255, 255, 255, 0.5);
					color: #888 !important;
				}

				<?php endforeach; ?>
			</style>

			<?php
			STM_E_W\Helpers\Helper::stm_ew_load_template(
				'widgets/add-listing/parts/additional_fields',
				MOTORS_ELEMENTOR_WIDGETS_PATH,
				array(
					'histories'             => $history_report,
					'post_id'               => $_id,
					'custom_listing_type'   => $custom_listing_type,
					'listing_types_options' => $listing_types_options,
				)
			);
			?>

			<?php
			if ( $details_location ) :
				$data_value            = get_post_meta( $_id, 'stm_car_location', true );
				$data_value_lat        = get_post_meta( $_id, 'stm_lat_car_admin', true );
				$data_value_lng        = get_post_meta( $_id, 'stm_lng_car_admin', true );
				$data_location_address = get_post_meta( $_id, 'stm_location_address', true );
				?>

				<div class="stn-add-car-location-wrap">
					<div class="stm-car-listing-data-single">
						<div class="title heading-font"><?php esc_html_e( 'Listing item Location', 'motors-elementor-widgets' ); ?></div>
					</div>
					<div class="stm-form-1-quarter stm_location stm-location-search-unit">
						<div class="stm-location-input-wrap stm-location">
							<div class="stm-label">
								<i class="stm-service-icon-pin_2"></i>
								<?php esc_html_e( 'Location', 'motors-elementor-widgets' ); ?>
							</div>
							<input type="text" name="stm_location_text"
								<?php
								if ( ! empty( $data_value ) ) {
									?>
									class="stm_has_value"
								<?php } ?> id="stm-add-car-location" value="<?php echo esc_attr( $data_value ); ?>" placeholder="<?php esc_attr_e( 'Enter ZIP or Address', 'motors-elementor-widgets' ); ?>"/>
						</div>
						<div class="stm-location-input-wrap stm-lng">
							<div class="stm-label">
								<i class="stm-service-icon-pin_2"></i>
								<?php esc_html_e( 'Latitude', 'motors-elementor-widgets' ); ?>
							</div>
							<input type="text" class="text_stm_lat" name="stm_lat" value="<?php echo esc_attr( $data_value_lat ); ?>" placeholder="<?php esc_attr_e( 'Enter Latitude', 'motors-elementor-widgets' ); ?>"/>
						</div>
						<div class="stm-location-input-wrap stm-lng">
							<div class="stm-label">
								<i class="stm-service-icon-pin_2"></i>
								<?php esc_html_e( 'Longitude', 'motors-elementor-widgets' ); ?>
							</div>
							<input type="text" class="text_stm_lng" name="stm_lng" value="<?php echo esc_attr( $data_value_lng ); ?>" placeholder="<?php esc_attr_e( 'Enter Longitude', 'motors-elementor-widgets' ); ?>"/>
						</div>
						<div class="stm-link-lat-lng-wrap">
							<a href="https://www.latlong.net/" target="_blank"><?php echo esc_html__( 'Lat and Long Finder', 'motors-elementor-widgets' ); ?></a>
						</div>
						<input type="hidden" name="stm_location_address" id="stm_location_address" value="<?php echo esc_attr( $data_location_address ); ?>">
					</div>
				</div>

			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
