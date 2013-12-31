<?php
/*
Plugin Name: Genesis Design Palette Pro - eNews Widget
Plugin URI: http://genesisdesignpro.com
Description: Targeted styling for Genesis eNews Extended widget
Author: Reaktiv Studios
Version: 0.0.1.1
Requires at least: 3.5
Author URI: http://reaktivstudios.com
*/
/*  Copyright 2013 Andrew Norcross, Josh Eaton

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License (GPL v2) only.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if( !defined( 'GPWEN_BASE' ) )
	define( 'GPWEN_BASE', plugin_basename(__FILE__) );

if( !defined( 'GPWEN_DIR' ) )
	define( 'GPWEN_DIR', dirname( __FILE__ ) );

if( !defined( 'GPWEN_VER' ) )
	define( 'GPWEN_VER', '0.0.1.1' );

class GP_Pro_Widget_Enews
{

	/**
	 * Static property to hold our singleton instance
	 * @var GP_Pro_Widget_Enews
	 */
	static $instance = false;

	/**
	 * This is our constructor
	 *
	 * @return GP_Pro_Widget_Enews
	 */
	private function __construct() {

		// general backend
		add_action			(	'plugins_loaded',					array(	$this,	'textdomain'				)			);
		add_action			(	'admin_notices',					array(	$this,	'gppro_active_check'		),	10		);
		add_action			(	'admin_notices',					array(	$this,	'enews_active_check'		),	10		);

		// GP Pro specific
		add_filter			(	'gppro_admin_block_add',			array(	$this,	'genesis_widgets_block'		),	61		);
		add_filter			(	'gppro_sections',					array(	$this,	'genesis_widgets_section'	),	10,	2	);

		// Defaults
		add_filter			(	'gppro_set_defaults',				array(	$this,	'enews_defaults_base'		),	15		);

		// GP Pro CSS build filters
		add_filter			(	'gppro_css_builder',				array(	$this,	'enews_widget_css'			),	10,	3	);

		// activation hooks
		register_deactivation_hook	( __FILE__,						array(	$this,	'enews_clear_check'		)			);
	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return GP_Pro_Widget_Enews
	 */

	public static function getInstance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;

	}

	/**
	 * load textdomain
	 *
	 * @return
	 */

	public function textdomain() {

		load_plugin_textdomain( 'gpwen', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * set widget dependency data
	 *
	 * @return widget dependency info
	 */

	static function plugin_info() {

		return array(
			'name'	=> __( 'Genesis eNews Extended', 'gpwen' ),
			'file'	=> 'genesis-enews-extended/plugin.php'
		);

	}

	/**
	 * check for GP Pro being active
	 *
	 * @return GP_Pro_Widget_Enews
	 */

	public function gppro_active_check() {

		$screen = get_current_screen();

		if ( $screen->parent_file !== 'plugins.php' )
			return;

		if ( !is_plugin_active( 'genesis-palette-pro/genesis-palette-pro.php' ) ) :

			echo '<div id="message" class="error fade below-h2"><p><strong>'.__( sprintf( 'This plugin requires Genesis Design Palette Pro to function.' ), 'gpwen' ).'</strong></p></div>';

			// hide activation method
			unset( $_GET['activate'] );

			deactivate_plugins( plugin_basename( __FILE__ ) );

		endif;

	}

	/**
	 * check for correct child theme being active
	 *
	 * @return notice
	 */

	public function enews_active_check() {

		$screen = get_current_screen();

		if ( $screen->base !== 'genesis_page_genesis-palette-pro' )
			return;

		// get our Genesis Plugin dependency name
		$plugininfo	= self::plugin_info();

		// check for dismissed setting
		$ignored	= get_option( 'gppro-warning-'.$plugininfo['file'] );

		if ( $ignored == 1 )
			return;

		// check if plugin is active, display warning
		$enews_plugin_active	= is_plugin_active( $plugininfo['file'] );

		if ( ! $enews_plugin_active ) :

			echo '<div id="message" class="error fade below-h2 gppro-admin-warning"><p>';
			echo '<strong>'.__( 'Warning: You have the '.$plugininfo['name'].' widget add-on enabled but do not have the '.$plugininfo['name'].' plugin active.', 'gpwen' ).'</strong>';
			echo '<span class="ignore" data-child="'.$plugininfo['file'].'">'.__( 'Ignore this message', 'gpwen' ).'</span>';
			echo '</p></div>';

		endif;


	}


	/**
	 * add block to side
	 *
	 * @return
	 */

	public function genesis_widgets_block( $blocks ) {

		$blocks['genesis-widgets'] = array(
			'tab'		=> __( 'Genesis Widgets', 'gpwen' ),
			'title'		=> __( 'Genesis Widgets', 'gpwen' ),
			'slug'		=> 'genesis_widgets',
		);

		return $blocks;

	}

	/**
	 * add section to side
	 *
	 * @return
	 */

	public function genesis_widgets_section( $sections, $class ) {

		$sections['genesis_widgets']	= array(

			'genesis-widget-setup'	=> array(
				'headline'	=> __( 'Genesis Widgets', 'gpwen' ),
				'intro'		=> __( 'Target and style individual widgets such as eNews Extended', 'gpwen' ),
				'title'		=> '',
				'data'		=> '',
			),

			'section-break-enews-widget-general'	=> array(
				'break'	=> array(
					'type'	=> 'full',
					'title'	=> __( 'eNews Widget', 'gpwen' ),
				),
			),

			'enews-widget-general'	=> array(
				'headline'	=> __( 'eNews Widget', 'gpwen' ),
				'intro'		=> __( 'Style subscription forms created with Genesis eNews Extended', 'gpwen' ),
				'title'		=> 'General Colors',
				'data'		=> array(
					'enews-widget-back'	=> array(
						'label'		=> __( 'Background', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget',
						'selector'	=> 'background-color'
					),
					'enews-widget-title-color'	=> array(
						'label'		=> __( 'Title Color', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget .widget-title',
						'selector'	=> 'color'
					),
					'enews-widget-text-color'	=> array(
						'label'		=> __( 'Text Color', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget',
						'selector'	=> 'color'
					),
				),
			),

			'enews-widget-typography'	=> array(
				'title'	=> __( 'Before and After Text Typography', 'gpwen' ),
				'data'	=> array(
					'enews-widget-gen-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gpwen' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .enews-widget p',
						'selector'	=> 'font-family'
					),
					'enews-widget-gen-size'	=> array(
						'label'		=> __( 'Font Size', 'gpwen' ),
						'input'		=> 'font-size',
						'scale'		=> 'text',
						'target'	=> $class.' .enews-widget p',
						'selector'	=> 'font-size',
					),
					'enews-widget-gen-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gpwen' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .enews-widget p',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gpwen' )
					),
					'enews-widget-gen-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gpwen' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .enews-widget p',
						'selector'	=> 'text-transform'
					),
					'enews-widget-gen-text-margin-bottom' => array(
						'label'		=> __( 'Bottom Margin', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget p',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '48',
						'step'		=> '1'
					),
				),
			),

			'enews-widget-field-inputs'	=> array(
				'title'		=> __( 'Field Inputs', 'gpwen' ),
				'data'		=> array(
					'enews-widget-field-input-back'	=> array(
						'label'		=> __( 'Background', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'background-color'
					),
					'enews-widget-field-input-text-color'	=> array(
						'label'		=> __( 'Font Color', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'color'
					),
					'enews-widget-field-input-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gpwen' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'font-family'
					),
					'enews-widget-field-input-size'	=> array(
						'label'		=> __( 'Font Size', 'gpwen' ),
						'input'		=> 'font-size',
						'scale'		=> 'text',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'font-size'
					),
					'enews-widget-field-input-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gpwen' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gpwen' )
					),
					'enews-widget-field-input-border-color'	=> array(
						'label'		=> __( 'Border Color', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'border-color',
					),
					'enews-widget-field-input-border-type'	=> array(
						'label'		=> __( 'Border Type', 'gpwen' ),
						'input'		=> 'borders',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'border-style',
						'tip'		=> __( 'Setting the type to "none" will remove the border completely.', 'gpwen' )
					),
					'enews-widget-field-input-border-width'	=> array(
						'label'		=> __( 'Border Width', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'border-width',
						'min'		=> '0',
						'max'		=> '10',
						'step'		=> '1'
					),
					'enews-widget-field-input-border-radius'	=> array(
						'label'		=> __( 'Border Radius', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'border-radius',
						'min'		=> '0',
						'max'		=> '16',
						'step'		=> '1'
					),
					'enews-widget-field-input-padding'	=> array(
						'label'		=> __( 'Field Padding', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'padding',
						'min'		=> '0',
						'max'		=> '24',
						'step'		=> '1'
					),
					'enews-widget-field-input-margin-bottom' => array(
						'label'		=> __( 'Bottom Margin', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '48',
						'step'		=> '1'
					),
					'enews-widget-field-input-box-shadow'	=> array(
						'label'		=> __( 'Box Shadow', 'gpwen' ),
						'input'		=> 'radio',
						'options'	=> array(
							array(
								'label'	=> __( 'Keep', 'gpwen' ),
								'value'	=> 'inherit',
							),
							array(
								'label'	=> __( 'Remove', 'gpwen' ),
								'value'	=> 'none'
							),
						),
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'selector'	=> 'box-shadow'
					),
				),
			),

			'enews-widget-button-colors'	=> array(
				'title'		=> __( 'Submit Button Colors', 'gpwen' ),
				'data'		=> array(
					'enews-widget-button-back'	=> array(
						'label'		=> __( 'Background', 'gpwen' ),
						'sub'		=> __( 'Base', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'background-color'
					),
					'enews-widget-button-back-hov'	=> array(
						'label'		=> __( 'Background', 'gpwen' ),
						'sub'		=> __( 'Hover', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget input:hover[type="submit"]',
						'selector'	=> 'background-color'
					),
					'enews-widget-button-text-color'	=> array(
						'label'		=> __( 'Font Color', 'gpwen' ),
						'sub'		=> __( 'Base', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'color'
					),
					'enews-widget-button-text-color-hov'	=> array(
						'label'		=> __( 'Font Color', 'gpwen' ),
						'sub'		=> __( 'Hover', 'gpwen' ),
						'input'		=> 'color',
						'target'	=> $class.' .enews-widget input:hover[type="submit"]',
						'selector'	=> 'color'
					),
				),
			),

			'enews-widget-button-typography'	=> array(
				'title'		=> __( 'Submit Button Typography', 'gpwen' ),
				'data'		=> array(
					'enews-widget-button-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gpwen' ),
						'input'		=> 'font-stack',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'font-family'
					),
					'enews-widget-button-size'	=> array(
						'label'		=> __( 'Font Size', 'gpwen' ),
						'input'		=> 'font-size',
						'scale'		=> 'text',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'font-size'
					),
					'enews-widget-button-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gpwen' ),
						'input'		=> 'font-weight',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gpwen' )
					),
					'enews-widget-button-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gpwen' ),
						'input'		=> 'text-transform',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'text-transform'
					),
					'enews-widget-button-margin-bottom' => array(
						'label'		=> __( 'Bottom Margin', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'margin-bottom',
						'min'		=> '0',
						'max'		=> '48',
						'step'		=> '1'
					),
				),
			),
			'enews-widget-button-padding'	=> array(
				'title'		=> __( 'Submit Button Padding', 'gpwen' ),
				'data'		=> array(
					'enews-widget-button-pad-top'	=> array(
						'label'		=> __( 'Top', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'padding-top',
						'min'		=> '0',
						'max'		=> '32',
						'step'		=> '1'
					),
					'enews-widget-button-pad-bottom'	=> array(
						'label'		=> __( 'Bottom', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '32',
						'step'		=> '1'
					),
					'enews-widget-button-pad-left'	=> array(
						'label'		=> __( 'Left', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'padding-left',
						'min'		=> '0',
						'max'		=> '32',
						'step'		=> '1'
					),
					'enews-widget-button-pad-right'	=> array(
						'label'		=> __( 'Right', 'gpwen' ),
						'input'		=> 'spacing',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'selector'	=> 'padding-right',
						'min'		=> '0',
						'max'		=> '32',
						'step'		=> '1'
					),
				),
			),

		); // end section


		return $sections;

	}

	public function enews_defaults_base( $defaults ) {

		// General
		$defaults['enews-widget-back']                          = '#333333';
		$defaults['enews-widget-title-color']                   = '#ffffff';
		$defaults['enews-widget-text-color']                    = '#999999';

		// General Typography
		$defaults['enews-widget-gen-stack']                     = 'lato';
		$defaults['enews-widget-gen-size']                      = '16';
		$defaults['enews-widget-gen-weight']                    = '300';
		$defaults['enews-widget-gen-transform']                 = 'none';
		$defaults['enews-widget-gen-text-margin-bottom']        = '24';

		// Field Inputs
		$defaults['enews-widget-field-input-back']              = '#ffffff';
		$defaults['enews-widget-field-input-text-color']        = '#999999';
		$defaults['enews-widget-field-input-stack']             = 'lato';
		$defaults['enews-widget-field-input-size']              = '14';
		$defaults['enews-widget-field-input-weight']            = '300';
		$defaults['enews-widget-field-input-border-color']      = '#dddddd';
		$defaults['enews-widget-field-input-border-type']       = 'solid';
		$defaults['enews-widget-field-input-border-width']      = '1';
		$defaults['enews-widget-field-input-border-radius']     = '3';
		$defaults['enews-widget-field-input-padding']           = '16';
		$defaults['enews-widget-field-input-margin-bottom']     = '16';
		$defaults['enews-widget-field-input-box-shadow']        = 'inherit';

		// Submit Button
		$defaults['enews-widget-button-back']                   = '#f15123';
		$defaults['enews-widget-button-back-hov']               = '#ffffff';
		$defaults['enews-widget-button-text-color']             = '#ffffff';
		$defaults['enews-widget-button-text-color-hov']         = '#333333';
		$defaults['enews-widget-button-transform']              = 'uppercase';
		$defaults['enews-widget-button-stack']                  = 'helvetica';
		$defaults['enews-widget-button-size']                   = '14';
		$defaults['enews-widget-button-weight']                 = '300';
		$defaults['enews-widget-button-pad-top']                = '16';
		$defaults['enews-widget-button-pad-bottom']             = '16';
		$defaults['enews-widget-button-pad-left']               = '24';
		$defaults['enews-widget-button-pad-right']              = '24';
		$defaults['enews-widget-button-margin-bottom']          = '0';

		// Allow child theme add-ons to override eNews defaults
		$defaults	= apply_filters( 'gppro_enews_set_defaults', $defaults );

		return $defaults;

	}

	public function enews_widget_css( $css, $data, $class ) {

		$css	.= '/* eNews Extended Widget */'."\n";

		// enews-widget
		$css	.= $class.' .enews-widget { ';

			// Colors
			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-back' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'background-color', $data['enews-widget-back'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-text-color' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'color', $data['enews-widget-text-color'] );

		$css	.= '}'."\n";

		// Sidebar Widget Background Color
		if ( GP_Pro_Builder::build_check( $data, 'enews-widget-back' ) )
			$css	.= $class.' .sidebar .enews-widget { ' . GP_Pro_Builder::hexcolor_css( 'background-color', $data['enews-widget-back'] ) . '}'."\n";

		// Widget Title Color
		if ( GP_Pro_Builder::build_check( $data, 'enews-widget-title-color' ) )
			$css	.= $class.' .enews-widget .widget-title { ' . GP_Pro_Builder::hexcolor_css( 'color', $data['enews-widget-title-color'] ) . '}'."\n";

		// Paragraphs (regular widget text Above & Below)
		$css	.= $class.' .enews-widget p { ';

			// Typography
			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-gen-stack' ) )
				$css	.= GP_Pro_Builder::stack_css( 'font-family', $data['enews-widget-gen-stack'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-gen-size' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'font-size', $data['enews-widget-gen-size'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-gen-weight' ) )
				$css	.= GP_Pro_Builder::number_css( 'font-weight', $data['enews-widget-gen-weight'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-gen-transform' ) )
				$css	.= GP_Pro_Builder::text_css( 'text-transform', $data['enews-widget-gen-transform'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-gen-text-margin-bottom' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'margin-bottom', $data['enews-widget-gen-text-margin-bottom'] );

		$css	.= '}'."\n";

		// Field Inputs
		$css	.= $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"] { ';

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-back' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'background-color', $data['enews-widget-field-input-back'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-text-color' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'color', $data['enews-widget-field-input-text-color'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-stack' ) )
				$css	.= GP_Pro_Builder::stack_css( 'font-family', $data['enews-widget-field-input-stack'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-size' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'font-size', $data['enews-widget-field-input-size'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-weight' ) )
				$css	.= GP_Pro_Builder::number_css( 'font-weight', $data['enews-widget-field-input-weight'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-border-color' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'border-color', $data['enews-widget-field-input-border-color'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-border-type' ) )
				$css	.= GP_Pro_Builder::text_css( 'border-style', $data['enews-widget-field-input-border-type'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-border-width' ) )
				$css	.= GP_Pro_Builder::px_css( 'border-width', $data['enews-widget-field-input-border-width'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-border-radius' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'border-radius', $data['enews-widget-field-input-border-radius'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-padding' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'padding', $data['enews-widget-field-input-padding'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-margin-bottom' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'margin-bottom', $data['enews-widget-field-input-margin-bottom'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-field-input-box-shadow' ) )
				$css	.= GP_Pro_Builder::text_css( 'box-shadow', $data['enews-widget-field-input-box-shadow'] );

		$css	.= '}'."\n";

		// Submit Button
		$css	.= $class.' .enews-widget input[type="submit"] { ';

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-back' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'background-color', $data['enews-widget-button-back'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-text-color' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'color', $data['enews-widget-button-text-color'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-stack' ) )
				$css	.= GP_Pro_Builder::stack_css( 'font-family', $data['enews-widget-button-stack'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-size' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'font-size', $data['enews-widget-button-size'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-weight' ) )
				$css	.= GP_Pro_Builder::number_css( 'font-weight', $data['enews-widget-button-weight'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-transform' ) )
				$css	.= GP_Pro_Builder::text_css( 'text-transform', $data['enews-widget-button-transform'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-pad-top' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'padding-top', $data['enews-widget-button-pad-top'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-pad-bottom' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'padding-bottom', $data['enews-widget-button-pad-bottom'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-pad-left' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'padding-left', $data['enews-widget-button-pad-left'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-pad-right' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'padding-right', $data['enews-widget-button-pad-right'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-margin-bottom' ) )
				$css	.= GP_Pro_Builder::px_rem_css( 'margin-bottom', $data['enews-widget-button-margin-bottom'] );

		$css	.= '}'."\n";

		// Submit Button Hover and Focus states
		$css	.= $class.' .enews-widget input:hover[type="submit"], '.$class.' .enews-widget input:focus[type="submit"] { ';

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-back-hov' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'background-color', $data['enews-widget-button-back-hov'] );

			if ( GP_Pro_Builder::build_check( $data, 'enews-widget-button-text-color-hov' ) )
				$css	.= GP_Pro_Builder::hexcolor_css( 'color', $data['enews-widget-button-text-color-hov'] );

		$css	.= '}'."\n";

		return $css;

	}

	/**
	 * clear warning check setting
	 *
	 * @return void
	 */

	public function enews_clear_check() {

		// get our plugin dependency name
		$plugininfo	= self::plugin_info();

		// delete the dismissed setting
		delete_option( 'gppro-warning-'.$plugininfo['file'] );

	}

/// end class
}

// Instantiate our class
$GP_Pro_Widget_Enews = GP_Pro_Widget_Enews::getInstance();

