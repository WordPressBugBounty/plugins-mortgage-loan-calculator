<?php

// Mortgage Loan Calculator widget
//
// Copyright (c) 2008-2021 MLCALC.COM
// http://www.mlcalc.com/free-widgets/mortgage-loan-calculator/wordpress.htm
//
// This is an add-on for WordPress
// http://wordpress.org/
//
// **********************************************************************
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// *****************************************************************
/*
Plugin Name: Mortgage Calculator
Plugin URI: https://www.mlcalc.com/free-widgets/mortgage-loan-calculator/wordpress.htm
Description: Powerful mortgage/loan calculator widget for your blog.
Author: mlcalc.com
Version: 1.5.24
Requires PHP: 7.4
Requires at least: 4.6
Author URI: https://www.mlcalc.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mortgage-loan-calculator
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('MLCALC_URL', plugins_url( '', __FILE__));
define('MLCALC_VERSION', '1.5.24');


// Numbers keep only digits and separators; the literal "null" survives because it hides the field.
function mlcalc_sanitize_number($value) {
	$value = trim((string) $value);
	if (strtolower($value) === 'null') return 'null';
	return preg_replace('/[^0-9.,]/', '', $value);
}

/* Function: display_mlcalc_widget
	** This function does the actual display of the widget in the sidebar
	**
	** args: $args (environment variables handled automatically by the hook)
	** returns: nothing
*/
function display_mlcalc_widget( $args ) {
	$args = wp_parse_args($args, array('before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => ''));
	$options = get_option( 'widget_mlcalc' );
	$title   = isset($options['title']) ? $options['title'] : '';

	echo wp_kses_post($args['before_widget']);
	echo wp_kses_post($args['before_title'])
		 . esc_html($title)
		 . wp_kses_post($args['after_title']);

	display_mlcalc($options);

	echo wp_kses_post($args['after_widget']);
}

/* Function: display_mlcalc
	** This function does the actual display of the common part of the widget
	**
	** args: $options - to skip duplicate get_options calls
	** returns: nothing
*/
function display_mlcalc($options = array(), $content = null, $code = "") {
	if(!empty($code) || (!empty($options) && !empty($options[0]) && ($options[0] == 'mlcalc'))){
		$shortcode = true;

		// $atts    ::= array of attributes
		// examples: [mlcalc]
		//           [mlcalc default='mortgage' size='narrow']
		$atts = shortcode_atts( array(
			// default parameters
			'default' => 'mortgage_only', // mortgage|loan|mortgage_only|loan_only
			'size' => 'wide', // wide|narrow
			'currency' => 'usd', // usd|eur|gbp|ENTER_YOURS
			'rate' => '4.5', // interest rate
			'price' => '300,000', // purchase price
			'mterm' => '30', // mortgage term
			'down' => '20', // downpayment in %
			'tax' => '3,000', // property tax
			'insurance' => '1,500', // property insurance
			'pmi' => '0.52',
			'amount' => '175,000', // loan amount
			'lterm' => '15', // loan term
			'schedule' => 'year_nc', // month|year|month_nc|year_nc|none (_nc stands for "no choice" for visitor)
			'language' => substr(get_bloginfo('language'), 0, 2), // en|de|es|fr|it|pt|ru
		), $options );

		$default_calculator = $atts['default'];
		$form_size          = ($atts['size'] == 'narrow' ? 'small' : $atts['size']);
		$currency_code      = $atts['currency'];
		$interest_rate      = $atts['rate'];
		$purchase_price     = $atts['price'];
		$mortgage_term      = $atts['mterm'];
		$down_payment       = $atts['down'];
		$property_tax       = $atts['tax'];
		$property_insurance = $atts['insurance'];
		$pmi                = $atts['pmi'];
		$loan_amount        = $atts['amount'];
		$loan_term          = $atts['lterm'];
		$amortization       = sanitize_text_field($atts['schedule']);
		$language           = $atts['language'];
	} else {
		$shortcode = false;

		$options = get_option( 'widget_mlcalc' );
		$default_calculator = empty( $options['default_calculator'] ) ? 'loan' : $options['default_calculator'];
		$form_size          = empty( $options['form_size'] ) ? 'small' : $options['form_size'];
		$currency_code      = empty( $options['currency_code'] ) ? 'usd' : $options['currency_code'];
		$interest_rate      = empty( $options['interest_rate'] ) ? '4.5' : $options['interest_rate'];
		$purchase_price     = empty( $options['purchase_price'] ) ? '300,000' : $options['purchase_price'];
		$mortgage_term      = empty( $options['mortgage_term'] ) ? '30' : $options['mortgage_term'];
		$down_payment       = empty( $options['down_payment'] ) ? '10' : $options['down_payment'];
		$property_tax       = empty( $options['property_tax'] ) ? '3,000' : $options['property_tax'];
		$property_insurance = empty( $options['property_insurance'] ) ? '1,500' : $options['property_insurance'];
		$pmi                = empty( $options['pmi'] ) ? '0.52' : $options['pmi'];
		$loan_amount        = empty( $options['loan_amount'] ) ? '175,000' : $options['loan_amount'];
		$loan_term          = empty( $options['loan_term'] ) ? '15' : $options['loan_term'];
		$amortization       = empty( $options['amortization'] ) ? 'year_nc' : $options['amortization'];
		$language           = empty( $options['language'] ) ? substr(get_bloginfo('language'), 0, 2) : $options['language'];
	};

	// Shortcode attributes and stored options are printed into HTML and inline JS:
	// numbers keep only digits and separators, the currency code only letters.
	$interest_rate      = mlcalc_sanitize_number($interest_rate);
	$purchase_price     = mlcalc_sanitize_number($purchase_price);
	$mortgage_term      = mlcalc_sanitize_number($mortgage_term);
	$down_payment       = mlcalc_sanitize_number($down_payment);
	$property_tax       = mlcalc_sanitize_number($property_tax);
	$property_insurance = mlcalc_sanitize_number($property_insurance);
	$pmi                = mlcalc_sanitize_number($pmi);
	$loan_amount        = mlcalc_sanitize_number($loan_amount);
	$loan_term          = mlcalc_sanitize_number($loan_term);
	$currency_code      = substr(preg_replace('/[^A-Za-z]/', '', (string) $currency_code), 0, 3);
	if($currency_code === '') $currency_code = 'usd';

	if($currency_code == 'eur') $currency_symbol = '&euro;';
	if($currency_code == 'gbp') $currency_symbol = '&pound;';
	if($currency_code == 'usd') $currency_symbol = '$';
	if(empty($currency_symbol)) $currency_symbol = $currency_code;

	if (!in_array($language, array('en', 'de', 'es', 'fr', 'it', 'pt', 'ru', 'al'), true)) {
		$language = 'en';
	}

	// true = the block is rendered hidden.
	$display_mortgage_form = $display_loan_form = $display_loan_only = $display_mortgage_only = $display_loan = $display_mortgage = true;

	switch ($default_calculator){
		case 'mortgage':
			$display_mortgage = $display_mortgage_form = false;
		break;
		case 'loan_only':
			$display_loan_only = $display_loan_form = false;
		break;
		case 'mortgage_only':
			$display_mortgage_only = $display_mortgage_form = false;
		break;
		case 'loan':
		default:
			$display_loan = $display_loan_form = false;
		break;
	}

	$hidden_as = '';
	$display_amortization = false;

	if (!in_array($amortization, array('month', 'year', 'month_nc', 'year_nc', 'none'), true)) {
		$amortization = 'year_nc';
	}

	if ((preg_match('/_nc/i', $amortization)) || $amortization == 'none') {
		$display_amortization = true;
		$hidden_as = str_replace('_nc', '', $amortization);
	}

	$as_year  = (bool) preg_match('/year/i', $amortization);
	$as_month = (bool) preg_match('/month/i', $amortization);

	$WL_DIR_PREFIX = $language."/";

	// LOAD SCRIPTS
	wp_enqueue_script( 'mlcalc-widget-script', plugins_url($WL_DIR_PREFIX.'widget-nj.js', __FILE__), array('jquery'), MLCALC_VERSION, true );
	wp_add_inline_script( 'mlcalc-widget-script',
		'mlcalc_currency_symbol = ' . wp_json_encode($currency_symbol) . ';'
		. 'mlcalc_amortization = ' . wp_json_encode($amortization) . ';'
		. 'var _mlcalc_preload_img = new Image(312,44);'
		. '_mlcalc_preload_img.src = ' . wp_json_encode(plugins_url('images/ajax-loader.gif', __FILE__)) . ';',
		'before' );

	// LOAD STYLES
	wp_register_style( 'mlcalc-form-small-style', plugins_url('widget-form-small.css', __FILE__), array(), MLCALC_VERSION );
	wp_register_style( 'mlcalc-form-style', plugins_url('widget-form.css', __FILE__), array(), MLCALC_VERSION );
	if($form_size == 'small'){
		wp_enqueue_style( 'mlcalc-form-small-style' );
	} else {
		wp_enqueue_style( 'mlcalc-form-style' );
	}

	if($shortcode) ob_start();

	echo "<!-- MLCALC BEGIN -->\r\n";
	include($WL_DIR_PREFIX."forms.inc.php");
	echo "<!-- MLCALC END -->\r\n";
	if($shortcode){
		$result = ob_get_contents();
		ob_end_clean();
		if(is_null($content)){
			return $result;
		} else {
			return $content . $result;
		}
	}
}

/* Function: mlcalc_control
**
** This function draws the controls form on the widget page and
** saves the settings when the "Save" button is clicked
**
** args: nothing
** returns: nothing
*/

function mlcalc_control() {
	$options = $newoptions = get_option('widget_mlcalc');
	if (!is_array($options)) $options = $newoptions = array();

	// Legacy widget controls are only called by core after it has checked the
	// "save-sidebar-widgets" nonce and the edit_theme_options capability.
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( !empty($_POST['mlcalc-submit'] ) && current_user_can('edit_theme_options') ) {
		$fields = array('title', 'default_calculator', 'form_size', 'currency_code', 'interest_rate', 'purchase_price', 'mortgage_term', 'down_payment', 'property_tax', 'property_insurance', 'pmi', 'loan_amount', 'loan_term', 'amortization', 'language');
		foreach ($fields as $field) {
			$newoptions[$field] = isset($_POST['mlcalc-' . $field]) ? sanitize_text_field(wp_unslash($_POST['mlcalc-' . $field])) : '';
		}
	}
	// phpcs:enable WordPress.Security.NonceVerification.Missing
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_mlcalc', $options);
	}

	$options = wp_parse_args($options, array_fill_keys(array('title', 'default_calculator', 'form_size', 'currency_code', 'interest_rate', 'purchase_price', 'mortgage_term', 'down_payment', 'property_tax', 'property_insurance', 'pmi', 'loan_amount', 'loan_term', 'amortization', 'language'), ''));
?>
		<p><label for="mlcalc-title"><?php esc_html_e('Title:', 'mortgage-loan-calculator'); ?> <input class="widefat" id="mlcalc-title" name="mlcalc-title" type="text" value="<?php echo esc_attr($options['title']); ?>" /></label></p>
		<p>
			<label for="mlcalc-default_calculator"><?php esc_html_e( 'Calculator:', 'mortgage-loan-calculator' ); ?>
				<select name="mlcalc-default_calculator" id="mlcalc-default_calculator" class="widefat">
					<option value="loan"<?php selected( $options['default_calculator'], 'loan' ); ?>><?php esc_html_e('Loan by Default', 'mortgage-loan-calculator'); ?></option>
					<option value="loan_only"<?php selected( $options['default_calculator'], 'loan_only' ); ?>><?php esc_html_e('Loan Only', 'mortgage-loan-calculator'); ?></option>
					<option value="mortgage"<?php selected( $options['default_calculator'], 'mortgage' ); ?>><?php esc_html_e('Mortgage by Default', 'mortgage-loan-calculator'); ?></option>
					<option value="mortgage_only"<?php selected( $options['default_calculator'], 'mortgage_only' ); ?>><?php esc_html_e('Mortgage Only', 'mortgage-loan-calculator'); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="mlcalc-form_size"><?php esc_html_e( 'Form size:', 'mortgage-loan-calculator' ); ?>
				<select name="mlcalc-form_size" id="mlcalc-form_size" class="widefat">
					<option value="small"<?php selected( $options['form_size'], 'small' ); ?>><?php esc_html_e('Narrow (width = 150px)', 'mortgage-loan-calculator'); ?></option>
					<option value="big"<?php selected( $options['form_size'], 'big' ); ?>><?php esc_html_e('Wide (width = 300px)', 'mortgage-loan-calculator'); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="mlcalc-amortization"><?php esc_html_e( 'Amortization:', 'mortgage-loan-calculator' ); ?>
				<select name="mlcalc-amortization" id="mlcalc-amortization" class="widefat">
					<option value="year_nc"<?php selected( $options['amortization'], 'year_nc' ); ?>><?php esc_html_e('Show by year (no selector)', 'mortgage-loan-calculator'); ?></option>
					<option value="month_nc"<?php selected( $options['amortization'], 'month_nc' ); ?>><?php esc_html_e('Show by month (no selector)', 'mortgage-loan-calculator'); ?></option>
					<option value="year"<?php selected( $options['amortization'], 'year' ); ?>><?php esc_html_e('Show by year', 'mortgage-loan-calculator'); ?></option>
					<option value="month"<?php selected( $options['amortization'], 'month' ); ?>><?php esc_html_e('Show by month', 'mortgage-loan-calculator'); ?></option>
					<option value="none"<?php selected( $options['amortization'], 'none' ); ?>><?php esc_html_e('Don\'t show', 'mortgage-loan-calculator'); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="mlcalc-language"><?php esc_html_e( 'Language:', 'mortgage-loan-calculator' ); ?>
				<select name="mlcalc-language" id="mlcalc-language" class="widefat">
					<option value="en"<?php selected( $options['language'], 'en' ); ?>>English</option>
					<option value="de"<?php selected( $options['language'], 'de' ); ?>>Deutsch</option>
					<option value="es"<?php selected( $options['language'], 'es' ); ?>>Espa&#241;ol</option>
					<option value="fr"<?php selected( $options['language'], 'fr' ); ?>>Fran&#231;ais</option>
					<option value="it"<?php selected( $options['language'], 'it' ); ?>>Italiana</option>
					<option value="pt"<?php selected( $options['language'], 'pt' ); ?>>Portugu&#234;s</option>
					<option value="ru"<?php selected( $options['language'], 'ru' ); ?>>&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;</option>
				</select>
			</label>
		</p>
		<p>
			<label for="mlcalc-currency_code"><?php esc_html_e( 'Currency symbol:', 'mortgage-loan-calculator' ); ?>
				<select name="mlcalc-currency_code" id="mlcalc-currency_code" class="widefat">
					<option value="usd"<?php selected( $options['currency_code'], 'usd' ); ?>><?php esc_html_e('USD ($)', 'mortgage-loan-calculator'); ?></option>
					<option value="eur"<?php selected( $options['currency_code'], 'eur' ); ?>><?php esc_html_e('EUR (&euro;)', 'mortgage-loan-calculator'); ?></option>
					<option value="gbp"<?php selected( $options['currency_code'], 'gbp' ); ?>><?php esc_html_e('GBP (&pound;)', 'mortgage-loan-calculator'); ?></option>
					<option value="R"<?php selected( $options['currency_code'], 'R' ); ?>><?php esc_html_e('R', 'mortgage-loan-calculator'); ?></option>
					<option value="Rp"<?php selected( $options['currency_code'], 'Rp' ); ?>><?php esc_html_e('Rp', 'mortgage-loan-calculator'); ?></option>
					<option value="AED"<?php selected( $options['currency_code'], 'AED' ); ?>><?php esc_html_e('AED', 'mortgage-loan-calculator'); ?></option>
					<option value="Php"<?php selected( $options['currency_code'], 'Php' ); ?>><?php esc_html_e('Php', 'mortgage-loan-calculator'); ?></option>
				</select>
			</label>
		</p>
		<p><a href="https://www.mlcalc.com/mortgage-calculator/" onclick="(typeof jQuery != 'undefined') ? jQuery('.mlcalc_default_values').show() : document.getElementById('mlcalc_default_values').style.display = 'inline'; this.style.display = 'none'; return false;">Change default values...</a></p>
		<span style="display:none" id="mlcalc_default_values" class="mlcalc_default_values">
		<p><label for="mlcalc-purchase_price"><?php esc_html_e( 'Purchase price', 'mortgage-loan-calculator' ); ?>: <br /> <input style="width: 65px; text-align: center;" type="text" value="<?php echo esc_attr($options['purchase_price']); ?>" name="mlcalc-purchase_price" id="mlcalc-purchase_price" /> $</label></p>
		<p><label for="mlcalc-down_payment"><?php esc_html_e( 'Down payment', 'mortgage-loan-calculator' ); ?> <small>(<a href="https://www.mlcalc.com/mortgage-calculator/" onclick="(typeof jQuery != 'undefined') ? jQuery('.mlcalc-down_payment').val('null') : document.getElementById('mlcalc-down_payment').value='null'; return false;">hide this field</a>)</small>: <br /> <input style="width: 35px; text-align: center;" type="text" value="<?php echo esc_attr($options['down_payment']); ?>" name="mlcalc-down_payment" id="mlcalc-down_payment" class="mlcalc-down_payment" /> %</label></p>
		<p><label for="mlcalc-mortgage_term"><?php esc_html_e( 'Mortgage term', 'mortgage-loan-calculator' ); ?>: <br /> <input style="width: 35px; text-align: center;" type="text" value="<?php echo esc_attr($options['mortgage_term']); ?>" name="mlcalc-mortgage_term" id="mlcalc-mortgage_term" /> years</label></p>
		<p><label for="mlcalc-interest_rate"><?php esc_html_e( 'Interest rate', 'mortgage-loan-calculator' ); ?>: <br /> <input style="width: 35px; text-align: center;" type="text" value="<?php echo esc_attr($options['interest_rate']); ?>" name="mlcalc-interest_rate" id="mlcalc-interest_rate" /> %</label></p>
		<p><label for="mlcalc-property_tax"><?php esc_html_e( 'Property tax', 'mortgage-loan-calculator' ); ?> <small>(<a href="https://www.mlcalc.com/mortgage-calculator/" onclick="(typeof jQuery != 'undefined') ? jQuery('.mlcalc-property_tax').val('null') : document.getElementById('mlcalc-property_tax').value='null'; return false;">hide this field</a>)</small>: <br /> <input style="width: 65px; text-align: center;" type="text" value="<?php echo esc_attr($options['property_tax']); ?>" name="mlcalc-property_tax" id="mlcalc-property_tax" class="mlcalc-property_tax" /> $ per year</label></p>
		<p><label for="mlcalc-property_insurance"><?php esc_html_e( 'Property insurance', 'mortgage-loan-calculator' ); ?> <small>(<a href="https://www.mlcalc.com/mortgage-calculator/" onclick="(typeof jQuery != 'undefined') ? jQuery('.mlcalc-property_insurance').val('null') : document.getElementById('mlcalc-property_insurance').value='null'; return false;">hide this field</a>)</small>: <br /> <input style="width: 65px; text-align: center;" type="text" value="<?php echo esc_attr($options['property_insurance']); ?>" name="mlcalc-property_insurance" id="mlcalc-property_insurance" class="mlcalc-property_insurance" /> $ per year</label></p>
		<p><label for="mlcalc-pmi"><?php esc_html_e( 'PMI', 'mortgage-loan-calculator' ); ?> <small>(<a href="https://www.mlcalc.com/mortgage-calculator/" onclick="(typeof jQuery != 'undefined') ? jQuery('.mlcalc-pmi').val('null') : document.getElementById('mlcalc-pmi').value='null'; return false;">hide this field</a>)</small>: <br /> <input style="width: 35px; text-align: center;" type="text" value="<?php echo esc_attr($options['pmi']); ?>" name="mlcalc-pmi" id="mlcalc-pmi" class="mlcalc-pmi" /> %</label></p>
		<p><label for="mlcalc-loan_amount"><?php esc_html_e( 'Loan amount', 'mortgage-loan-calculator' ); ?>: <br /> <input style="width: 65px; text-align: center;" type="text" value="<?php echo esc_attr($options['loan_amount']); ?>" name="mlcalc-loan_amount" id="mlcalc-loan_amount" /> $</label></p>
		<p><label for="mlcalc-loan_term"><?php esc_html_e( 'Loan term', 'mortgage-loan-calculator' ); ?>: <br /> <input style="width: 35px; text-align: center;" type="text" value="<?php echo esc_attr($options['loan_term']); ?>" name="mlcalc-loan_term" id="mlcalc-loan_term" /> years</label></p>
		</span>

		<input type="hidden" id="mlcalc-submit" name="mlcalc-submit" value="1" />
<?php
}

/* Function: mlcalc_register
**
** Registers the MLCALC widget with the widget page
**
** args: none
** returns: nothing
*/

function mlcalc_register() {
	$widget_ops = array('classname' => 'widget_mlcalc', 'description' => __('Mortgage and loan calculator', 'mortgage-loan-calculator'));
	$name = __('Mortgage Calculator', 'mortgage-loan-calculator');

	wp_register_sidebar_widget( 'mlcalc', $name, 'display_mlcalc_widget', $widget_ops );
	wp_register_widget_control( 'mlcalc', $name, 'mlcalc_control' );
}

// This is important
add_action( 'widgets_init', 'mlcalc_register' );
add_shortcode( 'mlcalc', 'display_mlcalc' );
