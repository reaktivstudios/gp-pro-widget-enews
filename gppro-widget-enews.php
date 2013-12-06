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


		); // end section


		return $sections;

	}

	public function enews_defaults_base( $defaults ) {

		// General
		$defaults['enews-widget-back']                  = '#333333';
		$defaults['enews-widget-title-color']           = '#ffffff';
		$defaults['enews-widget-text-color']            = '#999999';

		// General Typography
		$defaults['enews-widget-gen-stack']             = 'lato';
		$defaults['enews-widget-gen-size']              = '16';
		$defaults['enews-widget-gen-weight']            = '300';
		$defaults['enews-widget-gen-text-transform']    = 'none';

		// Text Input Colors

		// Text Input Typography

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

		return $css;

	}

/// end class
}

// Instantiate our class
$GP_Pro_Widget_Enews = GP_Pro_Widget_Enews::getInstance();

