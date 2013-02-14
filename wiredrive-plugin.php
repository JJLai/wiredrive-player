<?php

/*********************************************************************************
* Copyright (c) 2010 IOWA, llc dba Wiredrive
* Authors Wiredrive
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or* (at your option) any later version.
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
 * Wiredrive Plugin
 * Class that parses the shortcode and renders the html for the plugin
 *
 * Templates are run in this order:
 *   player_start.php
 *   flash.php OR html5.php
 *   thumb_loop.php
 *   Player_finish.php
 *
 * Flash is used if the browser is Firefox or IE,
 * otherwise HTML5 is used.  Additional CSS is added is the
 * browser is on a mobile device (added in player_start.php)
 *
 * mRSS feeds are required to come from the Wiredrive CDN servers originating
 * from www.wdcdn.net, and are not cached locally (they are cached on the
 * CDN already).
 *
 * mRSS is parsed by SimplePie built into Wordpress
 *
 * HTML5 Video playback in controlled by a the native browser player.
 *
 * Flash playback is controlled by a customized version of OVP
 * http://openvideoplayer.sourceforge.net/
 *
 */

class Wiredrive_Plugin
{
	protected $pluginUrl = NULL;
	protected $rss = NULL;
	protected $media = NULL;
	protected $template = NULL;
	protected $items = NULL;
	protected $isImageReel = FALSE;
	protected $rewriteBase = 'wdp-assets';
	protected $postId = NULL;
	protected $mediaGroup = array();
    protected $jsonpUrl = '';

	/**
	 * Contruct
	 * Register the plugin and start the template class
	 */
	public function __construct()
	{
		if ( function_exists('plugins_url') ) {
			$this->pluginUrl = plugins_url('wiredrive-player');
		}

		$this->template = new Wiredrive_Plugin_Template();

		/*
         * Get the post id from wordpress
         */
		$this->postId = the_Id();
	}

	/**
	 * Load the various scripts and css that are required by the plugin.
	 */
	public function init()
	{
		$plugin_url = plugins_url('wiredrive-player');

		wp_enqueue_script('jquery');
		wp_enqueue_script('swfobject');

		wp_register_script('wd-player', ($plugin_url  . '/js/wd-player.js'), 'jquery', '2.0');
		wp_enqueue_script('wd-player');

		wp_enqueue_style('wd-player', ($plugin_url . '/css/wd-player.css'));
	}

	/**
	 * Header
	 * Load the custom CSS.
	 */
	public function header()
	{
		$plugin_basename = plugin_basename('wiredrive-player');
		$options = get_option('wdp_options');
		
		echo $this->renderHead();
	}

	/**
	 * Render the player
	 * Imports the RSS feed and builds the video player
	 *
	 * @return string
	 */
	public function render($atts, $content = null )
	{

        /*
         * Get the settings for the plugin
         */
        $wiredriveSettings = new Wiredrive_Plugin_Settings();
        $options =  $wiredriveSettings->getOptions();

		/*
         * Get the height and width from the shortcode
         */
        $height = $options['height'] . 'px';
        $width  = $options['width'] . 'px';

		/*
         * Import the RSS feed
         */
		if (!$this->checkOrigin($content)) {
			$this->showError('Not a valid Wiredrive mRSS feed');
			return;
		}
		
        /*
         * check if the RSS feed is invalid
         */
		$result = $this->setRss($content);
        if (! $result) {
			$this->showError('Invalid Feed');
			return;
		}

		/*
         * Check if the RSS feed is empty
         */
		$items = $this->getRssItems();
		if (empty($items)) {
			$this->showError('Empty Feed');
			return;
		}

		/*
         * Loop through all the RSS items and build an
         * array for use in the templates
         */
        $this->_render($width, $height, $options);

		return $this->getOutput();

	}

	/**
	 * Determine is user is on an iPad
	 *
	 * @return bool
	 */
	public function isIpad()
	{
		return strpos($_SERVER['HTTP_USER_AGENT'], "iPad");

	}

	/**
	 * Determine is user is on a mobile device
	 *
	 * @return bool
	 */
	public function isMobile()
	{
		return (strpos($_SERVER['HTTP_USER_AGENT'], "iPhone")
			|| strpos($_SERVER['HTTP_USER_AGENT'], "Android"));

	}

	/**
	 * Determine if Flash player should be used
	 *
	 * @return bool
	 */
	public function useFlash()
	{
	   
	   /*
	    * Do not use falsh for ie9 because it supports h.264 natively with html5
	    */
	   
        /*
         * @TODO:  Once we have IE9 playing H.264 .MOV files, this should work.
           if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE 9.0")) {
               return false; 	   
          }
        */
	   
       return strpos($_SERVER['HTTP_USER_AGENT'], "Firefox")
			|| strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")			
			|| strpos($_SERVER['HTTP_USER_AGENT'], "Chrome");
	}

	/**
	 * URL Path to this plugin
	 *
	 * @return string
	 */
	public function getPluginUrl()
	{
		return $this->pluginUrl;
	}

	/**
	 * Unique id for flash video attribute
	 *
	 * @return string
	 */
	public function getAttributeId()
	{
		return "content-id-".uniqid();
	}

	/**
	 * Get the RSS feed and parse it with Wordpress built in
	 * SimplePie fetch_feed() function
	 */
	public function setRss($url)
	{
		$rss = fetch_feed($url);
        if (is_wp_error($rss)) {
		    return false;
		}
        $this->rss = $rss;
		$rss->enable_order_by_date(false);
	    
        $link = $rss->get_link();
        $url  = parse_url($link);
        if (! isset($url['path']) ||
            ! isset($url['scheme']) ||
            ! isset($url['host'])) {
            
            return false; 
        }
        $path = $url['path'];
        $path = trim($path, '/');
        $parts = explode('/', $path);
        $parts[0] = $parts[0] . '.jsonp';
        $this->jsonpUrl = $url['scheme'] . '://' . $url['host'] . '/' . 
                          implode('/', $parts);
        return true;
    }

	/**
	 * Make sure source of the feed is Wiredrive
	 */
	private function checkOrigin($url)
	{
        $domain = parse_url($url, PHP_URL_HOST);
		return $domain == 'www.wdcdn.net';
	}

	/**
	 * Get Rss
	 *
	 * @return SimplePie
	 */
	private function getRss()
	{
		return $this->rss;
	}

	/**
	 * Get Rss Items
	 *
	 * @return SimplePie
	 */
	private function getRssItems()
	{
		return $this->getRss()->get_items();
	}

	/**
	 * Set Media
	 * Get the Media enclosure from the rss feed
	 */
	private function setMedia($item)
	{
		$this->media = $item->get_enclosure();
	}

	/**
	 * Get Media
	 *
	 * @return SimplePie
	 */
	private function getMedia()
	{
		return $this->media;
	}
	
    /**
	 * Set Media Group
	 * Get the Media enclosure from the rss feed
	 *
	 * MediaGroup is an array of all the media:$group 
	 * elements for the item in the rss feed
	 */
	private function setMediaGroup($item, $group = 'thumbnail')
	{
		$this->mediaGroup = $item->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS,$group);
	}

	/**
	 * Get Media
	 *
	 * @return array
	 */
	private function getMediaGroup()
	{
		return $this->mediaGroup;
	}
	

	/**
	 * Items from the RSS formated as an array
	 * for the template top display
	 */
	private function setItems($items)
	{
		$this->items = $items;
	}

	/**
	 * Get Items
	 *
	 * @return array
	 */
	private function getItems()
	{
		return $this->items;
	}

	/**
	 * RSS feed consists entirely of images
	 */
	private function setIsImageReel($isImageReel)
	{
		$this->isImageReel = $isImageReel;
	}

	/**
	 * Get Is image reel
	 *
	 * @return bool
	 */
	private function getIsImageReel()
	{
		return $this->isImageReel;
	}

    private function _render($width, $height, $options) {
        $items = $this->getItems();
        $pluginUrl = $this->getPluginUrl();
        $type = 'video';
        $attributeId = $this->getAttributeId();

        if ($this->getIsImageReel()) {
            $type = 'image';
        } else if ($this->useFlash()) {
            $type = 'flash';
        }

        $this->template->setTpl('player.php')
             ->set('options', $options)
             ->set('attributeId', $attributeId)
             ->set('type', $type)
             ->set('height', $height)
             ->set('width', $width)
             ->set('pluginUrl', $pluginUrl)
             ->set('options', $options)
             ->set('jsonpUrl', $this->jsonpUrl)
             ->render();
    }

	/**
	 * Render any error
	 */
	private function showError($message)
	{
		$this->template->setTpl('error.php')
                ->set('message', $message)
                ->render();
	}

	/**
	 * Render any error
	 */
	private function renderHead()
	{
	
	    /*
         * Get the settings for the plugin
         */
        $wiredriveSettings = new Wiredrive_Plugin_Settings();
		   	
        $this->template->setTpl('head.php')
                ->set('options', $wiredriveSettings->getOptions())
                ->set('pluginUrl', $this->getPluginUrl())
                ->render();
		  
        return $this->template->getOutput();

	}
	
	/**
	 * Get outout
	 *
	 * @return string
	 */
	private function getOutput()
	{
		return $this->template->getOutput();
	}

	/**
	 * Get post id
	 */
	private function getPostId()
	{
		return $this->postId;

	}

}
