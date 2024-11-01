<?php
/**
 * @package Simpul0
 */
/*
Plugin Name: Simpul Facebook by Esotech
Plugin URI: http://www.esotech.org/plugins/simpul/simpul-facebook/
Description: This plugin is designed to access a facebook feed and display it in a Wordpress Widget.
Version: 2.2.6
Author: Alexander and Gregory Conroy
Author URI: http://www.esotech.org/people/alexander-conroy/
License: This code is released under the GPL licence version 3 or later, available here http://www.gnu.org/licenses/gpl.txt
*/

class SimpulFacebook extends WP_Widget 
{
					
	# The ID of the facebook feed we are trying to read	
	public function __construct()
	{
		$widget_ops = array('classname' => 'simpul-facebook', 
							'description' => 'A Simpul Facebook Widget' );
							
		parent::__construct('simpul_facebook', // Base ID
							'Facebook', // Name
							$widget_ops // Args  
							);
							
	}
	public function widget( $args, $instance )
	{
		extract($args);
		
		echo $before_widget;
		
		if($instance['title_element']):
			$before_title = '<' . $instance['title_element'] . ' class="widgettitle">';
			$after_title = '</' . $instance['title_element'] . '>';
		else:
			$before_title = '<h3 class="widgettitle">';
			$after_title = '</h3>';
		endif;
		
		
		if ( !empty( $instance['title']) ) { echo $before_title . $instance['title']. $after_title; };
		
		// Solution for caching.
		if($instance['cache_enabled']):
			
			if(!$instance['cache'] || current_time('timestamp') > strtotime($instance['cache_interval'] . ' hours', $instance['last_cache_time'])):
				$instance['cache'] = self::facebookStatus( $instance['account'] );
				$instance['last_cache_time'] = current_time('timestamp');
			endif;
			
			self::updateWidgetArray( $args, $instance );
			
		elseif($instance['cache'] || $instance['last_cache_time']):
			
			unset($instance['cache'], $instance['last_cache_time']);
			self::updateWidgetArray( $args, $instance );
			
		endif;
		
		$widget .= self::getPosts( $instance );
		
		echo $widget;
		
		echo $after_widget;
	}	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance 										= $old_instance;
		$instance['title'] 								= strip_tags($new_instance['title']);
		$instance['title_element'] 						= strip_tags($new_instance['title_element']);
		$instance['account'] 							= strip_tags($new_instance['account']);
		$instance['number'] 							= strip_tags($new_instance['number']);
		$instance['post_title'] 						= strip_tags($new_instance['post_title']);
		$instance['post_title_link'] 					= strip_tags($new_instance['post_title_link']);
		$instance['post_links'] 						= strip_tags($new_instance['post_links']);
		$instance['post_links_text'] 					= strip_tags($new_instance['post_links_text']);
		$instance['post_content'] 						= strip_tags($new_instance['post_content']);
		$instance['post_content_link'] 					= strip_tags($new_instance['post_content_link']);
		$instance['images'] 							= strip_tags($new_instance['images']);
		$instance['images_target'] 						= strip_tags($new_instance['images_target']);
		$instance['images_slide'] 						= strip_tags($new_instance['images_slide']);
		$instance['images_max'] 						= strip_tags($new_instance['images_max']);
		$instance['image_width'] 						= strip_tags($new_instance['image_width']);
		$instance['image_height'] 						= strip_tags($new_instance['image_height']);
		$instance['images_position'] 					= strip_tags($new_instance['images_position']);
		$instance['external_links'] 					= strip_tags($new_instance['external_links']);
		$instance['external_links_target'] 				= strip_tags($new_instance['external_links_target']);
		$instance['external_links_images'] 				= strip_tags($new_instance['external_links_images']);
		$instance['external_links_images_target'] 		= strip_tags($new_instance['external_links_images_target']);
		$instance['external_links_description'] 		= strip_tags($new_instance['external_links_description']);
		$instance['external_links_description_target'] 	= strip_tags($new_instance['external_links_description_target']);
		$instance['cache_enabled'] 						= strip_tags($new_instance['cache_enabled']);
		$instance['cache_interval']						= strip_tags($new_instance['cache_interval']);
		
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$instance 									= wp_parse_args( (array) $instance, array( 'title' => '', 'account' => 'esotech', 'number' => '3' ) );
		$title 										= strip_tags($instance['title']);
		$title_element								= strip_tags($instance['title_element']);
		$account 									= strip_tags($instance['account']);
		$number										= strip_tags($instance['number']);
		$post_title									= strip_tags($instance['post_title']);
		$post_title_link							= strip_tags($instance['post_title_link']);
		$post_links									= strip_tags($instance['post_links']);
		$post_links_text							= strip_tags($instance['post_links_text']);
		$post_content								= strip_tags($instance['post_content']);
		$post_content_link							= strip_tags($instance['post_content_link']);
		$images										= strip_tags($instance['images']);
		$images_target								= strip_tags($instance['images_target']);
		$images_slide								= strip_tags($instance['images_slide']);
		$images_max									= strip_tags($instance['images_max']);
		$image_width								= strip_tags($instance['image_width']);
		$image_height								= strip_tags($instance['image_height']);
		$images_position							= strip_tags($instance['images_position']);
		$external_links								= strip_tags($instance['external_links']);
		$external_links_target						= strip_tags($instance['external_links_target']);
		$external_links_images						= strip_tags($instance['external_links_images']);
		$external_links_images_target				= strip_tags($instance['external_links_images_target']);
		$external_links_description					= strip_tags($instance['external_links_description']);
		$external_links_description_target			= strip_tags($instance['external_links_description_target']);
		$cache_enabled 								= strip_tags($instance['cache_enabled']);
		$cache_interval 							= strip_tags($instance['cache_interval']);
		
		echo self::formatField($this->get_field_name('title'), $this->get_field_id('title'), $title, "Title");
		echo self::formatField($this->get_field_name('title_element'), $this->get_field_id('title_element'), $title_element, "Title Element (Default h3)");
		echo self::formatField($this->get_field_name('account'), $this->get_field_id('account'), $account, 'Facebook <a href="http://findmyfacebookid.com/" target="_BLANK">Account ID</a> (Page / Personal)' );
		echo "<h3>Post Options</h3>";
		echo self::formatField($this->get_field_name('number'), $this->get_field_id('number'), $number, "Number of Posts" );
		echo self::formatField($this->get_field_name('post_title'), $this->get_field_id('post_title'), $post_title, "Show Facebook Post Titles", 'checkbox' );
		echo self::formatField($this->get_field_name('post_title_link'), $this->get_field_id('post_title_link'), $post_title_link, "Link Title to Facebook Post", 'checkbox' );
		//
		echo self::formatField($this->get_field_name('post_links'), $this->get_field_id('post_links'), $post_links, "Show Facebook Post Link", 'checkbox' );
		echo self::formatField($this->get_field_name('post_links_text'), $this->get_field_id('post_links_text'), $post_links_text, "Facebook Post Link Text", 'text' );
		//
		echo self::formatField($this->get_field_name('post_content'), $this->get_field_id('post_content'), $post_content, "Show Post Text Content", 'checkbox' );
		echo self::formatField($this->get_field_name('post_content_link'), $this->get_field_id('post_content_link'), $post_content_link, "Link Text Content to Facebook Post", 'checkbox' );
		echo "<h3>Image Options</h3>";
		echo self::formatField($this->get_field_name('images'), $this->get_field_id('images'), $images, "Show Images", 'checkbox' );
		echo self::formatField($this->get_field_name('images_target'), $this->get_field_id('images_target'), $images_target, "Link Images to Facebook Post / Direct Image Link:", 'radio', '', array('off','facebook_post','image_link') );
		echo self::formatField($this->get_field_name('images_slide'), $this->get_field_id('images_slide'), $images_slide, "Show Gallery Images as a Slider", 'checkbox' );
		echo self::formatField($this->get_field_name('images_max'), $this->get_field_id('images_max'), $images_max, "Maximum Gallery Images to Display" );
		echo self::formatField($this->get_field_name('image_width'), $this->get_field_id('image_width'),  $image_width, "Image Width" );
		echo self::formatField($this->get_field_name('image_height'), $this->get_field_id('image_height'),  $image_height, "Image Height" );
		echo self::formatField($this->get_field_name('images_position'), $this->get_field_id('images_position'),  $images_position, "Images Position", 'radio', '', array('above','below') );
		echo "<h3>External Link Options</h3>";
		echo self::formatField($this->get_field_name('external_links'), $this->get_field_id('external_links'),  $external_links, "Show External Links" , 'checkbox');
		echo self::formatField($this->get_field_name('external_links_target'), $this->get_field_id('external_links_target'),  $external_links_target, "Link Anchor Text to External Links / Facebook Post:" , 'radio', '', array('off','external_link','facebook_post'));
		//
		echo self::formatField($this->get_field_name('external_links_images'), $this->get_field_id('external_links_images'),  $external_links_images, "Show External Link Preview Images" , 'checkbox');
		echo self::formatField($this->get_field_name('external_links_images_target'), $this->get_field_id('external_links_images_target'),  $external_links_images_target, "Link Images to External Links / Facebook Post:", 'radio', '', array('off','external_link','facebook_post'));
		echo "<h4>*Link Preview Images follow Image Options Height and Width</h4>";
		//
		echo self::formatField($this->get_field_name('external_links_description'), $this->get_field_id('external_links_description'),  $external_links_description, "Show External Link Descriptions" , 'checkbox');
		echo self::formatField($this->get_field_name('external_links_description_target'), $this->get_field_id('external_links_description_target'),  $external_links_description_target, "Link Descriptions to External Links / Facebook Post:" , 'radio', '', array('off','external_link','facebook_post'));
		echo "<h3>Cache Options</h3>";
		echo self::formatField($this->get_field_name('cache_enabled'), $this->get_field_id('cache_enabled'), $cache_enabled, "Use Cache?", 'checkbox' );
		echo self::formatField($this->get_field_name('cache_interval'), $this->get_field_id('cache_interval'),  $cache_interval, "Cache Interval (hours)" );
	}

	# -----------------------------------------------------------------------------#
	# End Standard Wordpress Widget Section
	# -----------------------------------------------------------------------------#
	
	# -----------------------------------------------------------------------------#
	# Get the facebook feed via CURL according to the facebook ID
	# -----------------------------------------------------------------------------#
	public function facebookStatus($facebook_id)
	{
		$header = array();
		$header[] = 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
		$header[] = 'Cache-Control: max-age=0';
		$header[] = 'Connection: keep-alive';
		$header[] = 'Keep-Alive: 300';
		$header[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
		$header[] = 'Accept-Language: en-us,en;q=0.5';
		$header[] = 'Pragma: ';
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, 'http://www.facebook.com/feeds/page.php?format=json&id='.$facebook_id);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.11) Gecko/2009060215 Firefox/3.0.11 (.NET CLR 3.5.30729)');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$response = curl_exec($ch);
		curl_close ($ch);
		
		return json_decode($response);
	}
	# -----------------------------------------------------------------------------#
	# This is the mthod we will call, which sets everything up and calls the
	# facebook_status method
	# -----------------------------------------------------------------------------#
	public function getPosts( $instance )
	{
		$result = null;
		$i = null;

		# get the actual feed
		
		if($instance['cache_enabled']):
			$wall = $instance['cache'];
		else:
			$wall = self::facebookStatus ($instance['account'] );
		endif;
		//print_r($wall);
		
		# Make sure we have something to work with
		
		if(!empty($wall))
		{
			$post_count = 1;
			foreach($wall->entries as $entry)
			{
				unset($post, $images, $text);
				
				if( $post_count > $instance['number'] )
					break;
				
				$post_count++;
				
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				// Good stuff below:
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// DATA TIME //////////////////////////////////////////////////////////////////////////////////////////////////////////
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				

				
				// NOT USED.
				$sample = array(
					'type' => '',
					'text' => 'text',
					'images' => array( 
						0 => array(
							'src',
							'href',
							'thumb')
					),
					'link' => array(
						'href' => '',
						'src' => '',
						'thumb' => '',
						'title' => '',
						'domain' => '',
						'description' => ''),
				);
				
				$IMAGES_REGEX = '/<img[^<].+?>)/';
				// Useful regexes used throughout.
				$FACEBOOK_REGEX = '/src=".*url=(.*?)\b"/';
				$FACEBOOK_IMAGE_SIZE_REGEX = '/_(\w)(.\w{3,4}$)/';
				$HTML_TAG_REGEX = '/<[^<]+?>/'; // <-- THIS IS AMAZING.
				$SRC_REGEX = '/.*src="(.+?)".*/';
				$HREF_REGEX = '/.*href="(.+?)".*/';
				$LINK_IMAGE_REGEX = '/<a.+?img.+?a>/';
				
				/**
				 * Collect data and/or manipulate as needed.
				 */
				// Grab all Links with images inside.
				preg_match_all($LINK_IMAGE_REGEX, $entry->content, $imagesData['images']);
				// Grab first result set.
				$imagesData['images'] = $imagesData['images'][0]; // Necessary due to preg_match_all.
				// Split all text by any element (HTML tags).
				$textData = preg_split($HTML_TAG_REGEX, $entry->content, -1, PREG_SPLIT_NO_EMPTY);
				// If text is blank, prepend a blank element.
				if( isset($textData[2]) && !isset($textData[3]) ) array_unshift($textData, '');
				
				// Determine type.
				$images_count = count($imagesData['images']);
				if( isset($textData[2]) ): // All links have at least a title and domain.
					$type = 'link';
				elseif( $images_count == 1 ):
					$type = 'image';
				elseif( $images_count > 1):
					$type = 'gallery';
				else:
					$type = 'text';
				endif;
				
				// Compensate for deleted/empty posts.
				if(empty($textData)):
					$post_count--;
					continue;
				endif;
				
				if($type != 'text'): // If $type is text, disregard image processing.
					// Process images.
					$count = 0;
					foreach( $imagesData['images'] as $item ):
						// Scrub any safe_image.php links out and replace with actual URL.
						$src = rawurldecode(preg_replace($FACEBOOK_REGEX, 'src="$1"', $item));
						// Grab src from img element.
						$src = rawurldecode(preg_replace($SRC_REGEX, '$1', $src));
						// Change from small to normal sized picture if there's a facebook image.
						$src = rawurldecode(preg_replace($FACEBOOK_IMAGE_SIZE_REGEX, '_n$2', $src));
						$tempArray[$count]['src'] = $src;
						// Grab href from a element.
						$href = rawurldecode(preg_replace($HREF_REGEX, '$1', $item));
						$tempArray[$count]['href'] = $href;
						// Generate simpulThumb.
						$tempArray[$count]['thumb'] = plugins_url( '/includes/simpulthumb.php', __FILE__ ) . 
							'?q=100' . 
							( !empty($instance['image_width']) ? '&w=' . $instance['image_width'] : '' ) . 
							( !empty($instance['image_height']) ? '&h=' . $instance['image_height'] : '' ) . 
							'&src=' . $src;
						unset($src, $href);
						$count++;
					endforeach;
					// Reassign $imagesData['images'].
					$imagesData['images'] = $tempArray;
					// Destroy temporary array used above.
					unset($tempArray);
				endif;
				
				// Process text.
				foreach($textData as $index => $value):
						// Destroy temporary array just in case.
						unset($tempArray);
						$tempArray['type'] = $type;
						$tempArray['text'] = $textData[0];
						// Do not insert images if $type is text.
						if($type != 'text') $tempArray['images'] = $imagesData['images'];
						// Assign link property array if $type is link.
						if($type == 'link'):
							// There may not be images, check first.
							if( !empty($imagesData['images'][0]['href']) ) $tempArray['link']['href'] = $imagesData['images'][0]['href'];
							if( !empty($imagesData['images'][0]['src']) ) $tempArray['link']['src'] = $imagesData['images'][0]['src'];
							if( !empty($imagesData['images'][0]['thumb']) ) $tempArray['link']['thumb'] = $imagesData['images'][0]['thumb'];
							// Destroy images array as it's no longer needed.
							unset($tempArray['images']); 
							// Assign link data.
							$tempArray['link']['title'] = $textData[1];
							$tempArray['link']['domain'] = $textData[2]; // Not yet used.
							$tempArray['link']['description'] = $textData[3];
						endif;
						break;
				endforeach;
				// Assign $theData.
				$theData = $tempArray;
				// Destroy temporary variables.
				unset($tempArray, $type, $textData, $imagesData);
				
				// Debugging.
				/*echo PHP_EOL . '<pre style="display: none;" post="' . ($post_count - 1) . '" type="' . $theData['type'] . '">' . PHP_EOL;
				//print_r($entry->content);
				//echo PHP_EOL;
				print_r($theData);
				echo PHP_EOL . '</pre>' . PHP_EOL;*/
				
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				// More good stuff below:
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////
				
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// DISPLAY TIME ///////////////////////////////////////////////////////////////////////////////////////////////////////
				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				$posts .= PHP_EOL . '<li post="' . ($post_count - 1) . '" type="' . $theData['type'] . '">' . PHP_EOL;
				
				// Prepare images based on type.
				switch($theData['type']):
					case 'gallery':
						$preparedImages = $theData['images'];
						break;
					case 'link':
						$preparedImages[] = $theData['link'];
						break;
					default:
						$preparedImages[] = $theData['images'][0];
						break;
				endswitch;
				
				/**
				 * Links and posts are different.
				 * The following applies to non-links.
				 */
				
				if( $theData['type'] != 'link' ):
					
					/**
					 * Post Title.
					 */
					 
					if( !empty($instance['post_title']) ):
						if( !empty($instance['post_title_link']) ):
							$text .= 
							self::createElement('a', array('class' => 'simpul-facebook-post-title-link', 'href' => $entry->alternate),
								self::createElement('p', array('class' => 'simpul-facebook-post-title'), 
									$entry->title
								)
							);
						else:
							$text .= 
							self::createElement('p', array('class' => 'simpul-facebook-post-title'), 
								$entry->title
							);
						endif;
					endif;
					
					/**
					 * Post Content.
					 */
					 
					if( !empty($instance['post_content']) ):
						if( !empty($instance['post_content_link']) ):
							$text .= 
							self::createElement('a', array('class' => 'simpul-facebook-post-content-link', 'href' => $entry->alternate),
								$text .= self::createElement('p', array('class' => 'simpul-facebook-post-content'),
									$theData['text']
								)
							);
						else:
							$text .= 
								self::createElement('p', array('class' => 'simpul-facebook-post-content'), 
									$theData['text']
							);
						endif;
					endif;
					
					/**
					 * Post Links. Appended after Post Content for visual flow.
					 */
					
					if( !empty($instance['post_links']) ):
						if( !empty($instance['post_links_text']) ):
							$text .= 
								self::createElement('a', array('class' => 'simpul-facebook-post-link', 'href' => $entry->alternate),
									self::createElement('p', array('class' => 'simpul-facebook-post-link-text'), $instance['post_links_text'])
							);
						else:
							$text .= 
								self::createElement('a', array('class' => 'simpul-facebook-post-link', 'href' => $entry->alternate),
									self::createElement('p', array('class' => 'simpul-facebook-post-link-text'), 'more')
							);
						endif;
					endif;
					
					/**
					 * Images.
					 */
					
					// $resize used for External Links Images as well.
					$resize = !(empty($instance['image_width']) && empty($instance['image_height'])); // Are we using resized images?
	
					if( $theData['type'] != 'link' && $theData['type'] != 'text' && !empty($instance['images']) ):
						
						$image_count = 0;
						foreach($preparedImages as $preparedImage):
							
							// Need image height for gallery div. 
							if(empty($galleryHeight) && !empty($instance['images_slide']) && $theData['type'] == 'gallery'):
								$galleryHeight = getimagesize($preparedImage['thumb']);
								$galleryHeight = $galleryHeight[1];
							endif;
							
							switch($instance['images_target']):
								case 'facebook_post':
									$images .=
									self::createElement('div', array('class' => 'simpul-facebook-image'),
										self::createElement('a', array('target' => '_blank', 'destination' => 'facebook_post', 'class' => 'simpul-facebook-image-href','href' => $entry->alternate), 
											self::createElement('img', array('src' => ($resize ? $preparedImage['thumb'] : $preparedImage['src']))
											)
										)
									);
									break;
								case 'image_link':
									$images .=
									self::createElement('div', array('class' => 'simpul-facebook-image'),
										self::createElement('a', array('target' => '_blank', 'destination' => 'image_link', 'class' => 'simpul-facebook-image-href', 'href' => $preparedImage['href']), 
											self::createElement('img', array('src' => ($resize ? $preparedImage['thumb'] : $preparedImage['src']))
											)
										)
									);
									break;
								default: // case 'off':
									$images .=
									self::createElement('div', array('class' => 'simpul-facebook-image'), 
										self::createElement('img', array('src' => ($resize ? $preparedImage['thumb'] : $preparedImage['src']))
										)
									);
									break;
							endswitch;
							$image_count++; if($image_count == $instance['images_max']) break; 
						endforeach;
						
						$images = 
						self::createElement('div', array('style' => (!empty($galleryHeight) ? 'position: relative; height: ' . $galleryHeight . 'px;' : ''), 'class' => 'simpul-facebook-images', 'slide' => !empty($instance['images_slide']) && $theData['type'] == 'gallery' ? 'yes': 'no'),
							$images
						);
						
					endif;
				
				/**
				 * The following applies to only links.
				 */
				
				else: // if( $theData['type'] != 'link' )
					
					/**
					 * External Links
					 */
					 
					if( !empty($instance['external_links']) ):
						switch($instance['external_links_target']):
								case 'facebook_post':
									$externalText .=
									self::createElement('a', array('target' => '_blank', 'destination' => 'facebook_post', 'class' => 'simpul-facebook-external-link-title-href','href' => $entry->alternate), 
										self::createElement('p', array('class' => 'simpul-facebook-external-link-title'),
											$theData['link']['title']
										)
									);
									break;
								case 'image_link':
									$externalText .=
									self::createElement('a', array('target' => '_blank', 'destination' => 'image_link', 'class' => 'simpul-facebook-external-link-title-href','href' => $theData['link']['href']), 
										self::createElement('p', array('class' => 'simpul-facebook-external-link-title'),
											$theData['link']['title']
										)
									);
									break;
								default: // case 'off':
									$externalText .= 
									self::createElement('p', array('class' => 'simpul-facebook-external-link-title'),
										$theData['link']['title']
									);
									break;
							endswitch;
					endif;
					 
					/*
					 * External Links Images
					 */
					
					// $resize used for External Links Images as well.
					$resize = !(empty($instance['image_width']) && empty($instance['image_height'])); // Are we using resized images?
					
					// Check to see if an image exists.
					$externalImageExists = !empty($theData['link']['href']) && !empty($theData['link']['src']) && !empty($theData['link']['thumb']);
					
					if( !empty($instance['external_links_images']) && $externalImageExists ):
						
						$image_count = 0;
						foreach($preparedImages as $preparedImage):
							switch($instance['external_links_images_target']):
								case 'facebook_post':
									$externalImages .=
									self::createElement('div', array('class' => 'simpul-facebook-image'),
										self::createElement('a', array('target' => '_blank', 'destination' => 'facebook_post', 'class' => 'simpul-facebook-image-href','href' => $entry->alternate), 
											self::createElement('img', array('src' => ($resize ? $preparedImage['thumb'] : $preparedImage['src']))
											)
										)
									);
									break;
								case 'image_link':
									$externalImages .=
									self::createElement('div', array('class' => 'simpul-facebook-image'),
										self::createElement('a', array('target' => '_blank', 'destination' => 'image_link', 'class' => 'simpul-facebook-image-href', 'href' => $preparedImage['href']), 
											self::createElement('img', array('src' => ($resize ? $preparedImage['thumb'] : $preparedImage['src']))
											)
										)
									);
									break;
								default: // case: 'off'
									$externalImages .= 
									self::createElement('div', array('class' => 'simpul-facebook-image'),
										self::createElement('img', array('src' => ($resize ? $preparedImage['thumb'] : $preparedImage['src']))
										)
									);
									break;
							endswitch;
							$image_count++; if($image_count == $instance['images_max']) break; 
						endforeach;
						
						$externalImages = 
						self::createElement('div', array('class' => 'simpul-facebook-images', 'slide' => !empty($instance['images_slide']) && $theData['type'] == 'gallery' ? 'yes': 'no'),
							$externalImages
						);
						
					endif;
					 
					/*
					 * External Links Description
					 */
					 
					if( !empty($instance['external_links_description']) ):
						switch($instance['external_links_description_target']):
								case 'facebook_post':
									$externalText .=
									self::createElement('a', array('target' => '_blank', 'destination' => 'facebook_post', 'class' => 'simpul-facebook-external-link-description-href','href' => $entry->alternate), 
										self::createElement('p', array('class' => 'simpul-facebook-external-link-description'),
											$theData['link']['description']
										)
									);
									break;
								case 'image_link':
									$externalText .=
									self::createElement('a', array('target' => '_blank', 'destination' => 'image_link', 'class' => 'simpul-facebook-external-link-description-href','href' => $theData['link']['href']), 
										self::createElement('p', array('class' => 'simpul-facebook-external-link-description'),
											$theData['link']['description']
										)
									);
									break;
								default: // case 'off':
									$externalText .= self::createElement('p', array('class' => 'simpul-facebook-external-link-description'),
										$theData['link']['description']
									);
									break;
							endswitch;
					endif;
				
				endif; // if( $theData['type'] != 'link' )

				// Prevent ternary operations in positioning due to External Links.
				if($theData['type'] == 'link'):
					$text = $externalText;
					$images = $externalImages;
				endif;
				
				/*
				 * Images position.
				 */
				
				if($instance['images_position'] == 'above'):
					$posts .= $images . $text;
				else:
					$posts .= $text . $images;
				endif;
				
				$posts .= PHP_EOL . '</li>' . PHP_EOL;
				
				// Destroy used variables just in case garbage collection hasn't.
				unset($preparedImages, $image_count, $text, $images, $resize, $externalText, $externalImages, $externalImageExists, $theData);
				
			}
			$posts = PHP_EOL . self::createElement('ul', array('feed' => 'http://www.facebook.com/feeds/page.php?format=json&id=' . $instance['account']), $posts) . PHP_EOL;
			return $posts;
		}
	return false;
	}
	public function createElement($type = 'div', $attributes = array(), $wrappedText = '') {
		$element = '';
		
		if($type == 'img' || $type == 'input') 
			$hasEndTag = FALSE;
		else
			$hasEndTag = TRUE;
		
		if( !empty($attributes) ):
			foreach($attributes as $attribute_name => $attribute_value):
				$attribute_array[] = $attribute_name . '="' . $attribute_value . '"';
			endforeach;
		endif;
			
		$element .= '<';
		$element .=  $type;
		$element .= !empty($attribute_array) ? ' ' . implode(' ', $attribute_array) : '';
		$element .= '>';
		$element .= $wrappedText;
		$element .= $hasEndTag ? '</' . $type . '>' : '';
		
		return PHP_EOL . $element . PHP_EOL;
	}
	public function textToLink($text)
	{
		$text  = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','<a href="\\1">\\1</a>', $text ); 
		$text  = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '\\1<a href="http://\\2">\\2</a>', $text ); 
		$text  = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})', '<a href="mailto:\\1">\\1</a>', $text );
		
		return $text;
	}
	public function getLabel($key)
	{
		$glued = array();
		if( strpos( $key, "_" ) ) $pieces = explode( "_", $key );
		elseif( strpos( $key, "-" ) ) $pieces = explode( "-", $key );
		else $pieces = explode(" ", $key);
		foreach($pieces as $piece):
			if($piece == "id"):
				$glued[] = strtoupper($piece);
			else:
				$glued[] = ucfirst($piece);
			endif;
		endforeach;
			
		return implode(" ", (array) $glued);
	}
	public function formatField($field, $id, $value, $description, $type = "text", $args = array(), $options = array() )	{
		if($type == "text"):
			return '<p>
					<label for="' . $id . '">
						' . $description . ': 
						<input class="widefat" id="' . $id . '" name="' . $field. '" type="text" value="' . attribute_escape($value) . '" />
					</label>
					</p>';
		elseif($type == "checkbox"):
			if( $value ) $checked = "checked";
			return '<p>
					<label for="' . $field . '">
						
						<input id="' . $field. '" name="' . $field . '" type="checkbox" value="1" ' . $checked . ' /> ' . $description . ' 
					</label>
					</p>';
		elseif($type == "radio"):
			$radio = '<p>
					<label for="' . $field . '">' . $description . '<br />';
					foreach($options as $option):
						if( $value == $option ): $checked = "checked"; else: $checked = ""; endif;						
						$radio .= '<input id="' . $field. '" name="' . $field . '" type="radio" value="' . $option . '" ' . $checked . ' /> ' . self::getLabel($option) . '<br />';
					endforeach; 
			$radio .= '</label>
					</p>';
			return $radio;
		endif;
	}
	public function updateWidgetArray( $args, $instance ) {
		
		$widget_class = explode('-', $args['widget_id']);
		$widget_id = array_pop($widget_class);
		$widget_name = implode('-', $widget_class);
		$widget_array = get_option('widget_' . $widget_name);
		
		$widget_array[$widget_id] = $instance;
		update_option('widget_' . $widget_name, $widget_array);
		
	}
}
//Register the Widget
function simpul_facebook_widget() {
	register_widget( 'SimpulFacebook' );
}
//Add Widget to wordpress
add_action( 'widgets_init', 'simpul_facebook_widget' );	

function simpul_facebook_scripts() {
	if( !wp_script_is('jquery') ):
		wp_enqueue_script( 'jquery' ); // Make sure jQuery is Enqueued
	endif;		
	
	if( !wp_script_is('cycle') ):
		wp_enqueue_script('cycle', plugins_url( '/js/jquery.cycle.all.js', __FILE__ ), array('jquery') );
	endif;
	
	wp_enqueue_script('simpul-facebook', plugins_url( '/js/simpul-facebook.js', __FILE__ ), array('jquery', 'cycle') );
}

if(!is_admin()):
	add_action( 'wp_enqueue_scripts', 'simpul_facebook_scripts' );	
endif;
