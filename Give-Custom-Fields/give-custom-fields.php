<?php
/**
 * Plugin Name: Give Custom Field Integration
 * Plugin URI: https://webspiderbd.com
 * Description: This plugin demonstrates adds custom fields to your Give forms with validation, email functionality, and field data output on the payment record within wp-admin.
 * Version: 1.2
 * Author: IBNUL HASAN
 * Author URI: https://www.upwork.com/o/profiles/users/_~01c0d51a3194de2650/
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 */
/**
 * Custom Form Fields
 *
 * @param $form_id
 */
 

function ibnul_give_donations_form_purpose_fields( $form_id ) {		
	
	if ( $form_id == 680) {
		?>
		<div id="give-purpose-wrap" class="form-row form-row-wide">
            <div class="radio">
			  <label><input type="radio" value="General" name="optradio" checked="checked">General</label>
			</div>
			<div class="radio">
			  <label><input type="radio" value="Current Fundraiser" name="optradio">Current Fundraiser</label>
			</div>
			<div class="radio">
			  <label><input type="radio" value="Planting" name="optradio">Planting</label>
			</div>
			<div class="radio">
			  <label><input type="radio" value="Public Education" name="optradio">Public Education</label>
			</div>
			<div class="radio">
			  <label><input type="radio" value="Conserving Land" name="optradio">Conserving Land</label>
			</div>
			<div class="radio">
			  <label><input type="radio" value="The Giving Tree Annual Fund" name="optradio">The Giving Tree Annual Fund</label>
			</div>
			<p>Please contact us if you are interested in donating land, establishing an endowment or contributing to a trust fund.</p>
		</div>
	<?php
	}
}
add_action( 'give_before_donation_levels', 'ibnul_give_donations_form_purpose_fields', 10, 1 );

function ibnul_give_donations_custom_form_fields( $form_id ) {
	// Only display for forms with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	//if ( $form_id == 953) {
	
	$forms = array( 953, 680 );
	if ( in_array( $form_id, $forms ) ) {
		?>
		<p id="give-phone-wrap" class="form-row form-row-last form-row-responsive">
            <label class="give-label" for="give-phone">
				<?php esc_html_e( 'Phone', 'give' ); ?>
				<?php if ( give_field_is_required( 'give_phone', $form_id ) ) : ?>
                    <span class="give-required-indicator">*</span>
				<?php endif ?>
                <span class="give-tooltip give-icon give-icon-question"
					data-tooltip="<?php esc_attr_e( 'We will use this to personalize your account experience.', 'give' ); ?>"></span>
            </label>
            <input
				class="give-input required"
				type="text"
				name="give_phone"
				placeholder="<?php esc_attr_e( 'Phone', 'give' ); ?>"
				id="give-phone"
				value="<?php echo isset( $give_user_info['give_phone'] ) ? $give_user_info['give_phone'] : ''; ?>"
			<?php echo( give_field_is_required( 'give_phone', $form_id ) ? ' required aria-required="true" ' : '' ); ?>
			/>
		</p>
	<?php
	}
}
add_action( 'give_donation_form_after_email', 'ibnul_give_donations_custom_form_fields', 10, 1 );
/**
 *
 * @param $required_fields
 * @param $form_id
 *
 * @return array
 */
function ibnul_give_donations_require_fields( $required_fields, $form_id ) {
	// Only validate the form with the IDs "754" and "578";
	// Remove "If" statement to display on all forms
	// For a single form, use this instead:
	//if ( $form_id == 953) {
	$forms = array( 953, 680 );
	if ( in_array( $form_id, $forms ) ) {		
		$required_fields['give_phone'] = array(
			'error_id'      => 'invalid_give_phone',
			'error_message' => __( 'Please enter your phone number', 'give' ),
		);
	/*	$required_fields['give_address'] = array(
			'error_id'      => 'invalid_give_address',
			'error_message' => __( 'Please enter your address', 'give' ),
		);  */
	}
	return $required_fields;
}
add_filter( 'give_donation_form_required_fields', 'ibnul_give_donations_require_fields', 10, 2 );
/**
 * Add Field to Payment Meta
 *
 * Store the custom field data custom post meta attached to the `give_payment` CPT.
 *
 * @param $payment_id
 * @param $payment_data
 *
 * @return mixed
 */
function ibnul_give_donations_save_custom_fields( $payment_id, $payment_data ) {
	if ( isset( $_POST['give_phone'] ) ) {
		$give_phone = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['give_phone'] ) ) );
		add_post_meta( $payment_id, 'give_phone', $give_phone );
	}
	if ( isset( $_POST['optradio'] ) ) {
		$optradio = $_POST['optradio'];
		add_post_meta( $payment_id, 'optradio', $optradio );
	}
}
add_action( 'give_insert_payment', 'ibnul_give_donations_save_custom_fields', 10, 2 );
/**
 * Show Data in Transaction Details
 *
 * Show the custom field(s) on the transaction page.
 *
 * @param $payment_id
 */
function ibnul_give_donations_purchase_details( $payment_id ) {
	$give_phone = give_get_meta( $payment_id, 'give_phone', true );
	if ( $give_phone ) : ?>

		<div id="give-phone" class="postbox">
			<h3 class="hndle"><?php esc_html_e( 'Donar Phone', 'give' ); ?></h3>
			<div class="inside" style="padding-bottom:10px;">
				<?php echo wpautop( $give_phone ); ?>
			</div>
		</div>

	<?php endif;
	$optradio = give_get_meta( $payment_id, 'optradio', true );
	if ( $optradio ) : ?>

		<div id="give-purpose" class="postbox">
			<h3 class="hndle"><?php esc_html_e( 'Purpose of Donation', 'give' ); ?></h3>
			<div class="inside" style="padding-bottom:10px;">
				<?php echo wpautop( $optradio ); ?>
			</div>
		</div>

	<?php endif;
}
add_action( 'give_view_donation_details_billing_before', 'ibnul_give_donations_purchase_details', 10, 1 );
/**
 * Adds a Custom "Engraved Message" Tag
 *
 * This function creates a custom Give email template tag.
 *
 * @param $payment_id
 */
function ibnul_add_sample_referral_tag( $payment_id ) {
	give_add_email_tag( 'give_phone', 'This outputs the Phone', 'ibnul_get_donation_referral_data' );
	give_add_email_tag( 'purpose_donation', 'This outputs the Purpose of Donation', 'ibnul_get_donation_purpose' );
}
add_action( 'give_add_email_tags', 'ibnul_add_sample_referral_tag' );
/**
 * Get Donation Referral Data
 *
 * Example function that returns Custom field data if present in payment_meta;
 * The example used here is in conjunction with the Give documentation tutorials.
 *
 * @param array $tag_args Array of arguments
 *
 * @return string
 */
function ibnul_get_donation_referral_data( $tag_args ) {
	$give_phone = give_get_meta( $tag_args['payment_id'], 'give_phone', true );

	$output = __( 'No referral data found.', 'give' );
	if ( !empty( $give_phone ) ) {
		$output = wp_kses_post( $give_phone );
	}
	
	return $output;
}
function ibnul_get_donation_purpose( $tag_args ) {
	$purpose_donation = give_get_meta( $tag_args['payment_id'], 'optradio', true );

	$output = __( 'No referral data found.', 'give' );
	if ( !empty( $purpose_donation ) ) {
		$output = wp_kses_post( $purpose_donation );
	}
	
	return $output;
}