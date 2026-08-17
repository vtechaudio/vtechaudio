<?php
/**
 * Shared multi-step form field helpers. Loaded once via functions.php so the
 * consultation and hire-request templates never redeclare them (which caused a
 * fatal "cannot redeclare" error). Each function is individually guarded.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'vtc_field' ) ) {
	function vtc_field( $label, $name, $type = 'text', $required = false, $ph = '' ) {
		$req = $required ? ' required' : '';
		$star = $required ? ' *' : '';
		echo '<div class="cf-field"><label for="cf-' . esc_attr( $name ) . '">' . esc_html( $label . $star ) . '</label>';
		if ( 'textarea' === $type ) {
			echo '<textarea id="cf-' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" rows="3"' . $req . ' placeholder="' . esc_attr( $ph ) . '"></textarea>';
		} else {
			echo '<input id="cf-' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" type="' . esc_attr( $type ) . '"' . $req . ' placeholder="' . esc_attr( $ph ) . '">';
		}
		echo '</div>';
	}
}
if ( ! function_exists( 'vtc_select' ) ) {
	function vtc_select( $label, $name, $options, $required = false ) {
		$req = $required ? ' required' : ''; $star = $required ? ' *' : '';
		echo '<div class="cf-field"><label for="cf-' . esc_attr( $name ) . '">' . esc_html( $label . $star ) . '</label>';
		echo '<select id="cf-' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '"' . $req . '><option value="">Select&hellip;</option>';
		foreach ( $options as $o ) { echo '<option>' . esc_html( $o ) . '</option>'; }
		echo '</select></div>';
	}
}
if ( ! function_exists( 'vtc_checks' ) ) {
	function vtc_checks( $label, $name, $options ) {
		echo '<div class="cf-field cf-field--full"><span class="cf-label">' . esc_html( $label ) . '</span><div class="cf-checkgrid">';
		foreach ( $options as $o ) {
			$id = 'cf-' . sanitize_title( $name . '-' . $o );
			echo '<label class="cf-check"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '[]" value="' . esc_attr( $o ) . '"> <span>' . esc_html( $o ) . '</span></label>';
		}
		echo '</div></div>';
	}
}
if ( ! function_exists( 'vtc_radios' ) ) {
	function vtc_radios( $label, $name, $options ) {
		echo '<div class="cf-field cf-field--full"><span class="cf-label">' . esc_html( $label ) . '</span><div class="cf-checkgrid">';
		foreach ( $options as $o ) {
			$id = 'cf-' . sanitize_title( $name . '-' . $o );
			echo '<label class="cf-check"><input type="radio" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $o ) . '"> <span>' . esc_html( $o ) . '</span></label>';
		}
		echo '</div></div>';
	}
}
if ( ! function_exists( 'vth_qty' ) ) {
	function vth_qty( $label, $name ) {
		echo '<div class="cf-field cf-field--qty"><label for="cf-' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label><input id="cf-' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" type="number" min="0" placeholder="0"></div>';
	}
}
