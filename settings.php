<?php

/*********************************************************************************
* Copyright (c) 2010 IOWA, llc dba Wiredrive
* Authors Drew Baker and Daniel Bondurant
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
********************************************************************************/

/**
 * Wiredrive Plugin Settings
 *
 * Build the global plugin settings and save in the database.
 */
class Wiredrive_Plugin_Settings
{
	private $defaults = array();
	private $options = array();
	private $optionsNs = 'wdp_options';

	public function __construct()
	{
		/*
         * Set up default values for plugin
         */
		$this->defaults = array(
			'width'                      => '640',
			'height'                     => '480',
			'stage_color'                => '#000000',
			'credit_container_border'    => '#2C2C2C',
			'credit_container_color'     => '#373636',
			'thumb_bg_color'             => '#141414',
			'arrow_color'                => '#EAEAEA',
			'active_item_color'          => '#FFFFFF',
			'title_color'                => '#FFFFFF',
			'credit_color'               => '#999999',
			'credit_container_alignment' => 'Center',
			'title_font_size'            => '12',
			'credit_font_size'           => '12',
			'thumb_box_opacity'          => '0.3',
		);

		/*
		 * Get options saved to the database
		 */
		$this->options = get_option($this->optionsNs);

	}

	/**
	 * Wdp Options Init
	 * Register our settings. Add the settings section, and settings fields
	 */
	public function options_init()
	{

		register_setting($this->optionsNs,
			$this->optionsNs,
			array($this, 'options_validate')
		);

		add_settings_section('main_section',
			'Main Settings',
			array($this, 'section_text'),
			__FILE__
		);

		add_settings_field('width',
			'Default Width',
			array($this, 'width'),
			__FILE__,
			'main_section'
		);

		add_settings_field('height',
			'Default Height',
			array($this, 'height'),
			__FILE__,
			'main_section'
		);
		
		add_settings_section('element_colors_section',
            'Element colors and properties',
            array($this, 'section_text'),
            __FILE__
        );

		add_settings_field('stage_color',
			'The color of the stage',
			array($this, 'stage_color'),
			__FILE__,
			'main_section'
		);
		
        add_settings_field('thumb_bg_color',
			'Thumb Tray Background Color',
			array($this, 'thumb_bg_color'),
			__FILE__,
			'main_section'
		);

		add_settings_field('credit_container_color',
			'Credit Container Color',
			array($this, 'credit_container_color'),
			__FILE__,
			'main_section'
		);

		add_settings_field('credit_container_border',
			"Credit Container's Top Border Color",
			array($this, 'credit_container_border'),
			__FILE__,
			'main_section'
		);

		add_settings_field('arrow_color',
			'Next & Previous Arrow Colors',
			array($this, 'arrow_color'),
			__FILE__,
			'main_section'
		);
		
		add_settings_field('active_item_color',
			'Active Item Color',
			array($this, 'active_item_color'),
			__FILE__,
			'main_section'
		);

        add_settings_field('thumb_box_opacity',
            'Pillarbox and letterbox opacity',
            array($this, 'thumb_box_opacity'),
            __FILE__,
            'element_colors_section'
        );
        
        add_settings_section('text_colors_section',
            'Text colors and properties',
            array($this, 'section_text'),
            __FILE__
        );
 
		add_settings_field('title_color',
			'Title Text Color',
			array($this, 'title_color'),
			__FILE__,
			'main_section'
		);
		
		add_settings_field('title_font_size',
            'Title size',
            array($this, 'title_font_size'),
            __FILE__,
            'text_colors_section'
        ); 

		add_settings_field('credit_color',
			'Credit Text Color',
			array($this, 'credit_color'),
			__FILE__,
			'main_section'
		);
		
		add_settings_field('credit_font_size',
            'Credit size',
            array($this, 'credit_font_size'),
            __FILE__,
            'text_colors_section'
        );
        
        add_settings_field('credit_container_alignment',
			'Credit Text Alignment',
			array($this, 'credit_container_alignment'),
			__FILE__,
			'main_section'
		);

	}

	/**
	 * Section Text
	 * Defaults to main_section on plugin page
	 */
	function section_text() { }

	/**
	 * Wiredrive Options Add
	 * Add sub page to the Settings Menu
	 */
	public function options_add_page()
	{
		add_options_page('Wiredrive Player Settings', '
		                  Wiredrive Player', 
		                 'administrator', 
		                 __FILE__, 
		                 array($this, 'options_page')
		                 );
	}

	/**
	 * Width
	 * input type      : textbox
	 * name            : width
	 */
	public function width()
	{
		$width = $this->getValue('width');
		echo $this->textboxInput($width,'width',false, true);
	}

	/**
	 * Height
	 * input type      : textbox
	 * name            : height
	 */
	public function height()
	{
		$height = $this->getValue('height');
		echo $this->textboxInput($height,'height',false, true);
	}

	/**
	 * Stage Color
	 * input type      : textbox
	 * name            : stage_color
	 */
	public function stage_color()
	{
		$stage_color = $this->getValue('stage_color');
		echo $this->textboxInput($stage_color,'stage_color',true);
	}

	/**
	 * Credit Container Border
	 * input type      : textbox
	 * name            : credit_container_border
	 */
	public function credit_container_border()
	{
		$credit_container_border = $this->getValue('credit_container_border');
		echo $this->textboxInput($credit_container_border,'credit_container_border',true);
	}

	/**
	 * Credit Container Color
	 * input type      : textbox
	 * name            : credit_container_color
	 */
	public function credit_container_color()
	{
		$credit_container_color = $this->getValue('credit_container_color');
		echo $this->textboxInput($credit_container_color,'credit_container_color',true);
	}

	/**
	 * Credit Container Alignment
	 * input type      : radio
	 * name            : credit_container_alignment
	 */
	public function credit_container_alignment()
	{
		$credit_container_alignment = $this->getValue('credit_container_alignment');

		$items = array("Left", "Center", "Right");
		foreach ($items as $item) {
			$checked = '';
			if ($credit_container_alignment == $item) {
				$checked = 'checked="checked"';
			}

			echo "<label><input ".
				$checked. " value='" .
				$item . "' name='". 
				$this->optionsNs ."[credit_container_alignment]' type='radio' /> " .
				$item . "</label><br />";
		}
	}

	/**
	 * Thumb Background Color
	 * input type      : textbox
	 * name            : thumb_bg_color
	 */
	public function thumb_bg_color()
	{
		$thumb_bg_color = $this->getValue('thumb_bg_color');
		echo $this->textboxInput($thumb_bg_color,'thumb_bg_color',true);
	}
	/**
	 * Arrow Color
	 * input type      : textbox
	 * name            : arrow_color
	 */
	public function arrow_color()
	{
		$arrow_color = $this->getValue('arrow_color');
		echo $this->textboxInput($arrow_color,'arrow_color',true);
	}
	/**
	 * Active Item Color
	 * input type      : textbox
	 * name            : active_item_color
	 */
	public function active_item_color()
	{
		$active_item_color = $this->getValue('active_item_color');
		echo $this->textboxInput($active_item_color,'active_item_color',true);
	}
	/**
	 * Title Color
	 * input type      : textbox
	 * Name            : title_color
	 */
	public function title_color()
	{
		$title_color = $this->getValue('title_color');
		echo $this->textboxInput($title_color,'title_color',true);
	}
	/**
	 * Create Color
	 * input type      : textbox
	 * name            : credit_color
	 */
	public function credit_color()
	{
		$credit_color = $this->getValue('credit_color');
		echo $this->textboxInput($credit_color,'credit_color',false);
	}
	
	/**
     * Title Font Size
     * input type       : textbox
     * name             : title_font_size
     */
     public function title_font_size()
	 {
		 $title_font_size = $this->getValue('title_font_size');
		 echo $this->textboxInput($title_font_size,'title_font_size',false, true);
	 }
	 
	 /**
      * Credit Font Size
      * input type      : textbox
      * name            : credit_font_size
      */
     public function credit_font_size()
	 {
		 $credit_font_size = $this->getValue('credit_font_size');
		 echo $this->textboxInput($credit_font_size,'credit_font_size',false, true);
	 }
	
     /**
      * Thumb Box Opacity
      * input type      : textbox
      * name            : thumb_box_opacity
      */
     public function thumb_box_opacity()
	 {
		 $thumb_box_opacity = $this->getValue('thumb_box_opacity');
		 echo $this->textboxInput($thumb_box_opacity,'thumb_box_opacity',false);
	 }
	        
	/**
	 * Options Validate
	 * Validate user data for some/all of your input fields
	 *
	 * @var input array
	 * @return array
	 */
	public function options_validate($input)
	{
		/*
		 * Filter textbox option fields to prevent HTML tags
		 */
		$clean['width']          = wp_filter_nohtml_kses($input['width']);
		$clean['height']         = wp_filter_nohtml_kses($input['height']);
		$clean['stage_color']    = wp_filter_nohtml_kses($input['stage_color']);
		$clean['thumb_bg_color'] = wp_filter_nohtml_kses($input['thumb_bg_color']);
		$clean['arrow_color']    = wp_filter_nohtml_kses($input['arrow_color']);
		$clean['title_color']    = wp_filter_nohtml_kses($input['title_color']);
		$clean['credit_color']   = wp_filter_nohtml_kses($input['credit_color']);
		
        $clean['active_item_color'] 
                   = wp_filter_nohtml_kses($input['active_item_color']);
        $clean['credit_container_color']      
		          = wp_filter_nohtml_kses($input['credit_container_color']);
		$clean['credit_container_border']     
		          = wp_filter_nohtml_kses($input['credit_container_border']);
		$clean['credit_container_alignment']  
		          = wp_filter_nohtml_kses($input['credit_container_alignment']);

        $clean['title_font_size']   
                    = wp_filter_nohtml_kses($input['title_font_size']);
        $clean['credit_font_size']  
                    = wp_filter_nohtml_kses($input['credit_font_size']);
        $clean['thumb_box_opacity'] 
                    = wp_filter_nohtml_kses($input['thumb_box_opacity']);
                    
		return $clean;
	}

	/**
	 * Options Page
	 * Display the admin options page
	 */
	public function options_page()
	{
?>
	<div class="wdp-settings wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Wiredrive Player Settings</h2>
		You can config the Wiredrive Player's appearance below.
		<form action="options.php" method="post">
		<?php settings_fields($this->optionsNs); ?>
		<?php do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" 
			 value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
	}

    /**
     * Get Values
     * Get the values stored in the database
     * fallback to defaults with they are not set
     *
	 * @var options array
	 * @return array
	 */
	private function getValue($option)
	{
		if (isset($this->options[$option])) {
			return $this->options[$option];
		}

		return $this->defaults[$option];
	}
	
	/**
	 * Text Box Input
	 * Format input box with options color wheel
	 * @var $value string
	 * @var $inputName string
	 * @var $showColors bool
	 * @var $showPx bool
	 *
	 * @return string
	 * 
	 */
	private function textboxInput($value,$inputName,$showColors = false, $showPx = false)
    {
        
		$str  =  "<div class='wdp-color-input-wrap'>";
		$str .= "<input id='". $inputName . "'";
		
		if ($showColors == true) {
		  $str .= "class='wdp-colorpicker' ";
		}
		
		$str .= "name='". $this->optionsNs ."[". $inputName . "]' size='10' type='text' value='" .
		          $value . "' />";
		          
        if ($showColors == true) {
			$str .= "<span class='wdp-color-button'></span>";
		    $str .= "<div class='wdp-color-picker-wrap'></div>";
        }
        
        if ($showPx == true) {
		   $str .=  " <span>px</span>";	
		}
		
		$str .= "</div>";
		
		
		return $str;
    }

}