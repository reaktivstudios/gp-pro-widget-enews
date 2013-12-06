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

		// GP Pro specific
		add_filter			(	'gppro_admin_block_add',			array(	$this,	'genesis_widgets_block'		),	61		);
		add_filter			(	'gppro_sections',					array(	$this,	'genesis_widgets_section'	),	10,	2	);

		// Defaults
		add_filter			(	'gppro_set_defaults',				array(	$this,	'enews_defaults_base'		),	15		);

		// GP Pro CSS build filters
		add_filter			(	'gppro_css_builder',				array(	$this,	'enews_widget_css'		),	10,	3	);
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
	 * check for GP Pro being active
	 *
	 * @return GP_Pro_Widget_Enews
	 */

	public function gppro_active_check() {

		$screen = get_current_screen();

		if ( $screen->parent_file !== 'plugins.php' )
			return;

		if ( !is_plugin_active( 'genesis-palette-pro/genesis-palette-pro.php' ) || !is_plugin_active( 'genesis-enews-extended/plugin.php' ) ) :

			echo '<div id="message" class="error fade below-h2"><p><strong>'.__( 'This plugin requires Genesis Design Palette Pro and Genesis eNews Extended to function.', 'gpwen' ).'</strong></p></div>';

			// hide activation method
			unset( $_GET['activate'] );

			deactivate_plugins( plugin_basename( __FILE__ ) );

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

			'enews-widget-general'	=> array(
				'headline'	=> __( 'eNews Widget', 'gpwen' ),
				'intro'		=> __( 'Style subscription forms created with Genesis eNews Extended', 'gpwen' ),
				'title'		=> 'General Colors',
				'data'		=> array(
					'enews-widget-back'	=> array(
						'label'		=> __( 'Background', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-back',
						'target'	=> $class.' .enews-widget',
						'type'		=> 'background-color'
					),
					'enews-widget-title-color'	=> array(
						'label'		=> __( 'Title Color', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-title-color',
						'target'	=> $class.' .enews-widget .widget-title',
						'type'		=> 'color'
					),
					'enews-widget-text-color'	=> array(
						'label'		=> __( 'Text Color', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-text-color',
						'target'	=> $class.' .enews-widget',
						'type'		=> 'color'
					),
				),
			),

			'enews-widget-typography'	=> array(
				'title'	=> __( 'Before and After Text Typography', 'gpwen' ),
				'data'	=> array(
					'enews-widget-gen-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gpwen' ),
						'input'		=> 'font-stack',
						'field'		=> 'enews-widget-gen-stack',
						'target'	=> $class.' .enews-widget',
						'type'		=> 'font-family'
					),
					'enews-widget-gen-size'	=> array(
						'label'		=> __( 'Font Size', 'gpwen' ),
						'input'		=> 'font-size',
						'scale'		=> 'text',
						'field'		=> 'enews-widget-gen-size',
						'target'	=> $class.' .enews-widget',
						'type'		=> 'font-size',
					),
					'enews-widget-gen-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gpwen' ),
						'input'		=> 'font-weight',
						'field'		=> 'enews-widget-gen-weight',
						'target'	=> $class.' .enews-widget',
						'type'		=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gpwen' )
					),
					'enews-widget-gen-text-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gpwen' ),
						'input'		=> 'text-transform',
						'field'		=> 'enews-widget-gen-text-transform',
						'target'	=> $class.' .enews-widget p',
						'type'		=> 'text-transform'
					),
				),
			),

			'enews-widget-field-inputs'	=> array(
				'title'		=> __( 'Field Inputs', 'gpwen' ),
				'data'		=> array(
					'enews-widget-field-input-back'	=> array(
						'label'		=> __( 'Background', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-field-input-back',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'background-color'
					),
					'enews-widget-field-input-text'	=> array(
						'label'		=> __( 'Font Color', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-field-input-text',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'color'
					),
					'enews-widget-field-input-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gpwen' ),
						'input'		=> 'font-stack',
						'field'		=> 'enews-widget-field-input-stack',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'font-family'
					),
					'enews-widget-field-input-size'	=> array(
						'label'		=> __( 'Font Size', 'gpwen' ),
						'input'		=> 'font-size',
						'scale'		=> 'text',
						'field'		=> 'enews-widget-field-input-size',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'font-size'
					),
					'enews-widget-field-input-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gpwen' ),
						'input'		=> 'font-weight',
						'field'		=> 'enews-widget-field-input-weight',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gpwen' )
					),
					'enews-widget-field-input-border-color'	=> array(
						'label'		=> __( 'Border Color', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-field-input-border-color',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'border-color',
					),
					'enews-widget-field-input-border-type'	=> array(
						'label'		=> __( 'Border Type', 'gpwen' ),
						'input'		=> 'borders',
						'field'		=> 'enews-widget-field-input-border-type',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'border-style',
						'tip'		=> __( 'Setting the type to "none" will remove the border completely.', 'gpwen' )
					),
					'enews-widget-field-input-border-width'	=> array(
						'label'		=> __( 'Border Width', 'gpwen' ),
						'input'		=> 'spacing',
						'field'		=> 'enews-widget-field-input-border-width',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'border-width',
						'min'		=> '0',
						'max'		=> '10',
						'step'		=> '1'
					),
					'enews-widget-field-input-border-radius'	=> array(
						'label'		=> __( 'Border Radius', 'gpwen' ),
						'input'		=> 'spacing',
						'field'		=> 'enews-widget-field-input-border-radius',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'border-radius',
						'min'		=> '0',
						'max'		=> '16',
						'step'		=> '1'
					),
					'enews-widget-field-input-padding'	=> array(
						'label'		=> __( 'Field Padding', 'gpwen' ),
						'input'		=> 'spacing',
						'field'		=> 'enews-widget-field-input-padding',
						'target'	=> $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"]',
						'type'		=> 'padding',
						'min'		=> '0',
						'max'		=> '24',
						'step'		=> '1'
					),
				),
			),

			'enews-widget-button-colors'	=> array(
				// 'headline'	=> __( 'eNews Submit Button', 'gpwen' ),
				'title'		=> __( 'eNews Submit Button Colors', 'gpwen' ),
				'data'		=> array(
					'enews-widget-button-back'	=> array(
						'label'		=> __( 'Background', 'gpwen' ),
						'sub'		=> __( 'Base', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-button-back',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'background-color'
					),
					'enews-widget-button-back-hov'	=> array(
						'label'		=> __( 'Background', 'gpwen' ),
						'sub'		=> __( 'Hover', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-button-back-hov',
						'target'	=> $class.' .enews-widget input:hover[type="submit"], '.$class.' .enews-widget input:focus[type="submit"]',
						'type'		=> 'background-color'
					),
					'enews-widget-button-text'	=> array(
						'label'		=> __( 'Font Color', 'gpwen' ),
						'sub'		=> __( 'Base', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-button-text',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'color'
					),
					'enews-widget-button-text-hov'	=> array(
						'label'		=> __( 'Font Color', 'gpwen' ),
						'sub'		=> __( 'Hover', 'gpwen' ),
						'input'		=> 'color',
						'field'		=> 'enews-widget-button-text-hov',
						'target'	=> $class.' .enews-widget input:hover[type="submit"], '.$class.' .enews-widget input:focus[type="submit"]',
						'type'		=> 'color'
					),
				),
			),

			'enews-widget-button-typography'	=> array(
				'title'		=> __( 'eNews Submit Button Typography', 'gpwen' ),
				'data'		=> array(
					'enews-widget-button-stack'	=> array(
						'label'		=> __( 'Font Stack', 'gpwen' ),
						'input'		=> 'font-stack',
						'field'		=> 'enews-widget-button-stack',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'font-family'
					),
					'enews-widget-button-size'	=> array(
						'label'		=> __( 'Font Size', 'gpwen' ),
						'input'		=> 'font-size',
						'scale'		=> 'text',
						'field'		=> 'enews-widget-button-size',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'font-size'
					),
					'enews-widget-button-weight'	=> array(
						'label'		=> __( 'Font Weight', 'gpwen' ),
						'input'		=> 'font-weight',
						'field'		=> 'enews-widget-button-weight',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'font-weight',
						'tip'		=> __( 'Certain fonts will not display every weight.', 'gpwen' )
					),
					'enews-widget-button-transform'	=> array(
						'label'		=> __( 'Text Appearance', 'gpwen' ),
						'input'		=> 'text-transform',
						'field'		=> 'enews-widget-button-transform',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'text-transform'
					),
				),
			),
			'enews-widget-button-padding'	=> array(
				'title'		=> __( 'eNews Submit Button Padding', 'gpwen' ),
				'data'		=> array(
					'enews-widget-button-pad-top'	=> array(
						'label'		=> __( 'Top', 'gpwen' ),
						'input'		=> 'spacing',
						'field'		=> 'enews-widget-button-pad-top',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'padding-top',
						'min'		=> '0',
						'max'		=> '32',
						'step'		=> '1'
					),
					'enews-widget-button-pad-bot'	=> array(
						'label'		=> __( 'Bottom', 'gpwen' ),
						'input'		=> 'spacing',
						'field'		=> 'enews-widget-button-pad-bot',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'padding-bottom',
						'min'		=> '0',
						'max'		=> '32',
						'step'		=> '1'
					),
					'enews-widget-button-pad-left'	=> array(
						'label'		=> __( 'Left', 'gpwen' ),
						'input'		=> 'spacing',
						'field'		=> 'enews-widget-button-pad-left',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'padding-left',
						'min'		=> '0',
						'max'		=> '32',
						'step'		=> '1'
					),
					'enews-widget-button-pad-right'	=> array(
						'label'		=> __( 'Right', 'gpwen' ),
						'input'		=> 'spacing',
						'field'		=> 'enews-widget-button-pad-right',
						'target'	=> $class.' .enews-widget input[type="submit"]',
						'type'		=> 'padding-right',
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
		$defaults['enews-widget-gen-text-transform']            = 'none';

		// Field Inputs
		$defaults['enews-widget-field-input-back']              = '#ffffff';
		$defaults['enews-widget-field-input-text']              = '#999999';
		$defaults['enews-widget-field-input-stack']             = 'lato';
		$defaults['enews-widget-field-input-size']              = '14';
		$defaults['enews-widget-field-input-weight']            = '300';
		$defaults['enews-widget-field-input-border-color']      = '#dddddd';
		$defaults['enews-widget-field-input-border-type']       = 'solid';
		$defaults['enews-widget-field-input-border-width']      = '1';
		$defaults['enews-widget-field-input-border-radius']     = '3';
		$defaults['enews-widget-field-input-padding']           = '16';

		// Submit Button
		$defaults['enews-widget-button-back']                   = '#f15123';
		$defaults['enews-widget-button-back-hov']               = '#ffffff';
		$defaults['enews-widget-button-text']                   = '#ffffff';
		$defaults['enews-widget-button-text-hov']               = '#333333';
		$defaults['enews-widget-button-transform']              = 'uppercase';
		$defaults['enews-widget-button-stack']                  = 'helvetica';
		$defaults['enews-widget-button-size']                   = '14';
		$defaults['enews-widget-button-weight']                 = '300';
		$defaults['enews-widget-button-pad-top']                = '16';
		$defaults['enews-widget-button-pad-bot']                = '16';
		$defaults['enews-widget-button-pad-left']               = '24';
		$defaults['enews-widget-button-pad-right']              = '24';

		// Allow child theme add-ons to override eNews defaults
		$defaults	= apply_filters( 'gppro_enews_set_defaults', $defaults );

		return $defaults;

	}

	public function enews_widget_css( $css, $data, $class ) {

		$css	.= '/* eNews Extended Widget */'."\n";

		// enews-widget
		$css	.= $class.' .enews-widget { ';

			// Colors
			if ( isset( $data['enews-widget-back'] ) && !empty( $data['enews-widget-back'] ) && $data['enews-widget-back'] !== GP_Pro_Helper::get_default( 'enews-widget-back' ) )
				$css	.= 'background-color: '.$data['enews-widget-back'].'; ';

			if ( isset( $data['enews-widget-text-color'] ) && !empty( $data['enews-widget-text-color'] ) && $data['enews-widget-text-color'] !== GP_Pro_Helper::get_default( 'enews-widget-text-color' ) )
				$css	.= 'color: '.$data['enews-widget-text-color'].'; ';

		$css	.= '}'."\n";

		// Sidebar Widget Background Color
		if ( isset( $data['enews-widget-back'] ) && !empty( $data['enews-widget-back'] ) && $data['enews-widget-back'] !== GP_Pro_Helper::get_default( 'enews-widget-back' ) )
			$css	.= $class.' .sidebar .enews-widget { background-color: '.$data['enews-widget-back'].'; }'."\n";

		// Widget Title Color
		if ( isset( $data['enews-widget-title-color'] ) && !empty( $data['enews-widget-title-color'] ) && $data['enews-widget-title-color'] !== GP_Pro_Helper::get_default( 'enews-widget-title-color' ) )
			$css	.= $class.' .enews-widget .widget-title { color: '.$data['enews-widget-title-color'].'; }'."\n";

		// Paragraphs (regular widget text Above & Below)
		$css	.= $class.' .enews-widget p { ';

			// Typography
			if ( isset( $data['enews-widget-gen-stack'] ) && !empty( $data['enews-widget-gen-stack'] ) && $data['enews-widget-gen-stack'] !== GP_Pro_Helper::get_default( 'enews-widget-gen-stack' ) )
				$css	.= 'font-family: '.GP_Pro_Builder::stack_css( $data['enews-widget-gen-stack'] ).'; ';

			if ( isset( $data['enews-widget-gen-size'] ) && !empty( $data['enews-widget-gen-size'] ) && $data['enews-widget-gen-size'] !== GP_Pro_Helper::get_default( 'enews-widget-gen-size' ) )
				$css	.= 'font-size: '.$data['enews-widget-gen-size'].'px; font-size: '.( $data['enews-widget-gen-size'] / 10 ).'rem; ';

			if ( isset( $data['enews-widget-gen-weight'] ) && !empty( $data['enews-widget-gen-weight'] ) && $data['enews-widget-gen-weight'] !== GP_Pro_Helper::get_default( 'enews-widget-gen-weight' ) )
				$css	.= 'font-weight: '.$data['enews-widget-gen-weight'].'; ';

			if ( isset( $data['enews-widget-gen-transform'] ) && !empty( $data['enews-widget-gen-transform'] ) && $data['enews-widget-gen-transform'] !== GP_Pro_Helper::get_default( 'enews-widget-gen-transform' ) )
				$css	.= 'text-transform: '.$data['enews-widget-gen-transform'].'; ';

		$css	.= '}'."\n";

		// Field Inputs
		$css	.= $class.' .enews-widget input[type="text"], '.$class.' .enews-widget input[type="email"] { ';

			if ( isset( $data['enews-widget-field-input-back'] ) && !empty( $data['enews-widget-field-input-back'] ) && $data['enews-widget-field-input-back'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-back' ) )
				$css	.= 'background-color: '.$data['enews-widget-field-input-back'].'; ';

			if ( isset( $data['enews-widget-field-input-text'] ) && !empty( $data['enews-widget-field-input-text'] ) && $data['enews-widget-field-input-text'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-text' ) )
				$css	.= 'color: '.$data['enews-widget-field-input-text'].'; ';

			if ( isset( $data['enews-widget-field-input-stack'] ) && !empty( $data['enews-widget-field-input-stack'] ) && $data['enews-widget-field-input-stack'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-stack' ) )
				$css	.= 'font-family: '.GP_Pro_Builder::stack_css( $data['enews-widget-field-input-stack'] ).'; ';

			if ( isset( $data['enews-widget-field-input-size'] ) && !empty( $data['enews-widget-field-input-size'] ) && $data['enews-widget-field-input-size'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-size' ) )
				$css	.= 'font-size: '.$data['enews-widget-field-input-size'].'px; font-size: '.( $data['enews-widget-field-input-size'] / 10 ).'rem; ';

			if ( isset( $data['enews-widget-field-input-weight'] ) && !empty( $data['enews-widget-field-input-weight'] ) && $data['enews-widget-field-input-weight'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-weight' ) )
				$css	.= 'font-weight: '.$data['enews-widget-field-input-weight'].'; ';

			if ( isset( $data['enews-widget-field-input-border-color'] ) && !empty( $data['enews-widget-field-input-border-color'] ) && $data['enews-widget-field-input-border-color'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-border-color' ) )
				$css	.= 'border-color: '.$data['enews-widget-field-input-border-color'].'; ';

			if ( isset( $data['enews-widget-field-input-border-type'] ) && !empty( $data['enews-widget-field-input-border-type'] ) && $data['enews-widget-field-input-border-type'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-border-type' ) )
				$css	.= 'border-style: '.$data['enews-widget-field-input-border-type'].'; ';

			if ( isset( $data['enews-widget-field-input-border-width'] ) && !empty( $data['enews-widget-field-input-border-width'] ) && $data['enews-widget-field-input-border-width'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-border-width' ) )
				$css	.= 'border-width: '.$data['enews-widget-field-input-border-width'].'px; ';

			if ( isset( $data['enews-widget-field-input-border-radius'] ) && !empty( $data['enews-widget-field-input-border-radius'] ) && $data['enews-widget-field-input-border-radius'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-border-radius' ) )
				$css	.= 'border-radius: '.$data['enews-widget-field-input-border-radius'].'px; ';

			if ( isset( $data['enews-widget-field-input-padding'] ) && !empty( $data['enews-widget-field-input-padding'] ) && $data['enews-widget-field-input-padding'] !== GP_Pro_Helper::get_default( 'enews-widget-field-input-padding' ) )
				$css	.= 'padding: '.$data['enews-widget-field-input-padding'].'px; padding: '.( $data['enews-widget-field-input-padding'] / 10 ).'rem; ';

		$css	.= '}'."\n";

		// Submit Button
		$css	.= $class.' .enews-widget input[type="submit"] { ';

			if ( isset( $data['enews-widget-button-back'] ) && !empty( $data['enews-widget-button-back'] ) && $data['enews-widget-button-back'] !== GP_Pro_Helper::get_default( 'enews-widget-button-back' ) )
				$css	.= 'background-color: '.$data['enews-widget-button-back'].'; ';

			if ( isset( $data['enews-widget-button-text'] ) && !empty( $data['enews-widget-button-text'] ) && $data['enews-widget-button-text'] !== GP_Pro_Helper::get_default( 'enews-widget-button-text' ) )
				$css	.= 'color: '.$data['enews-widget-button-text'].'; ';

			if ( isset( $data['enews-widget-button-stack'] ) && !empty( $data['enews-widget-button-stack'] ) && $data['enews-widget-button-stack'] !== GP_Pro_Helper::get_default( 'enews-widget-button-stack' ) )
				$css	.= 'font-family: '.GP_Pro_Builder::stack_css( $data['enews-widget-button-stack'] ).'; ';

			if ( isset( $data['enews-widget-button-size'] ) && !empty( $data['enews-widget-button-size'] ) && $data['enews-widget-button-size'] !== GP_Pro_Helper::get_default( 'enews-widget-button-size' ) )
				$css	.= 'font-size: '.$data['enews-widget-button-size'].'px; font-size: '.( $data['enews-widget-button-size'] / 10 ).'rem; ';

			if ( isset( $data['enews-widget-button-weight'] ) && !empty( $data['enews-widget-button-weight'] ) && $data['enews-widget-button-weight'] !== GP_Pro_Helper::get_default( 'enews-widget-button-weight' ) )
				$css	.= 'font-weight: '.$data['enews-widget-button-weight'].'; ';

			if ( isset( $data['enews-widget-button-transform'] ) && !empty( $data['enews-widget-button-transform'] ) && $data['enews-widget-button-transform'] !== GP_Pro_Helper::get_default( 'enews-widget-button-transform' ) )
				$css	.= 'text-transform: '.$data['enews-widget-button-transform'].'; ';

			if ( isset( $data['enews-widget-button-pad-top'] ) && !empty( $data['enews-widget-button-pad-top'] ) && $data['enews-widget-button-pad-top'] !== GP_Pro_Helper::get_default( 'enews-widget-button-pad-top' ) )
				$css	.= 'padding-top: '.$data['enews-widget-button-pad-top'].'px; padding-top: '.( $data['enews-widget-button-pad-top'] / 10 ).'rem; ';

			if ( isset( $data['enews-widget-button-pad-bot'] ) && !empty( $data['enews-widget-button-pad-bot'] ) && $data['enews-widget-button-pad-bot'] !== GP_Pro_Helper::get_default( 'enews-widget-button-pad-bot' ) )
				$css	.= 'padding-bottom: '.$data['enews-widget-button-pad-bot'].'px; padding-bottom: '.( $data['enews-widget-button-pad-bot'] / 10 ).'rem; ';

			if ( isset( $data['enews-widget-button-pad-left'] ) && !empty( $data['enews-widget-button-pad-left'] ) && $data['enews-widget-button-pad-left'] !== GP_Pro_Helper::get_default( 'enews-widget-button-pad-left' ) )
				$css	.= 'padding-left: '.$data['enews-widget-button-pad-left'].'px; padding-left: '.( $data['enews-widget-button-pad-left'] / 10 ).'rem; ';

			if ( isset( $data['enews-widget-button-pad-right'] ) && !empty( $data['enews-widget-button-pad-right'] ) && $data['enews-widget-button-pad-right'] !== GP_Pro_Helper::get_default( 'enews-widget-button-pad-right' ) )
				$css	.= 'padding-right: '.$data['enews-widget-button-pad-right'].'px; padding-right: '.( $data['enews-widget-button-pad-right'] / 10 ).'rem; ';

		$css	.= '}'."\n";

		// Submit Button Hover and Focus states
		$css	.= $class.' .enews-widget input:hover[type="submit"], '.$class.' .enews-widget input:focus[type="submit"] { ';

			if ( isset( $data['enews-widget-button-back-hov'] ) && !empty( $data['enews-widget-button-back-hov'] ) && $data['enews-widget-button-back-hov'] !== GP_Pro_Helper::get_default( 'enews-widget-button-back-hov' ) )
				$css	.= 'background-color: '.$data['enews-widget-button-back-hov'].'; ';

			if ( isset( $data['enews-widget-button-text-hov'] ) && !empty( $data['enews-widget-button-text-hov'] ) && $data['enews-widget-button-text-hov'] !== GP_Pro_Helper::get_default( 'enews-widget-button-text-hov' ) )
				$css	.= 'color: '.$data['enews-widget-button-text-hov'].'; ';

		$css	.= '}'."\n";

		return $css;

	}

/// end class
}

// Instantiate our class
$GP_Pro_Widget_Enews = GP_Pro_Widget_Enews::getInstance();

