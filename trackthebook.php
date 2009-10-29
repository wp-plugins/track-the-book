<?php
/*
Plugin Name: Track The Book
Plugin URI: http://www.hclibrary.org/trackthebook
Description: Allows visitors to manually enter their location and book number. A KML file is dynamically generated and can be displayed in a map using a WordPress plugin like XML Google Maps.
Author: Howard County Library
Version: 1.0
Author URI: http://www.hclibrary.org
*/

/*  Copyright 2009  Danny Bouman

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*
 * TODO: Add frontend for filters
 * TODO: Look at possiblity of a showing all locations a book has been
 */

// Define user feedback messages
define ("TTB_SUCCESS",__('<p>Your book number has been successfully registered! Thank you for helping us track your book as it moves around the world.</p><p>It may take a few minutes before you see your book marked on the map.</p><a href="#" id="TTB_close">Return to main page</a>'));
define ("TTB_ERROR_MISSING", __("Please make sure to fill out all of the required fields."));
define ("TTB_ERROR_500", __("We are sorry, an error has occurred while processing your request. Please try registering your book again."));
define ("TTB_ERROR_601", __("Please be sure to fill in your location."));
define ("TTB_ERROR_602", __("We are sorry, but despite our best efforts we were unable to find the location you specified. <br />Suggestions:<ul><li>Make sure all street and city names are spelled correctly.</li><li>Make sure your address includes a city and state</li><li>Try entering a zip code</li></ul>"));
define ("TTB_ERROR_603", TTB_ERROR_602); // Technically this is for an unavailable address, but for our purposes it is the same to the user
define ("TTB_ERROR_610", __("An invalid API Key has been specified, please contact the site administrator and let them know of this problem."));
define ("TTB_ERROR_620", __("We are sorry, too many requests have been placed today and we were unable to register your book. Please try again tomorrow."));

// Define a few plugin directories
define( 'TTB_BASENAME', plugin_basename( __FILE__ ) );
define( 'TTB_BASEFOLDER', plugin_basename( dirname( __FILE__ ) ) );
define('TTB_URL', get_option('siteurl').'/wp-content/plugins/' . TTB_BASEFOLDER);

// Define database version
define ("TTB_DB_VERSION","1.0");

// Define date format
define ("TTB_DATE_FORMAT","m/d/y");

global $wpdb;

// Include required files
require_once(dirname(__FILE__).'/trackthebook-admin.php');
require_once(dirname(__FILE__).'/trackthebook-model.php');
require_once(dirname(__FILE__).'/trackthebook-view.php');

if (!class_exists("TrackTheBook")) {
	class TrackTheBook {
		
		var $google_api_key;
		var $randomize_identical_coords;
		var $error;
		
		/**
		 * Constructor
		 */
		function TrackTheBook() { 	
			$trackthebook_options = get_option('trackthebook_options');
			$this->randomize_identical_coords = $trackthebook_options['randomize'];
			if (!empty($trackthebook_options['google_api_key']) && isset($trackthebook_options['google_api_key'])) {
				$this->google_api_key = $trackthebook_options['google_api_key'];
			}
		}
		
		/**
		 * Lookup a location and return coordinates
		 * @param $query Is the location to lookup
		 * @param $simple_query Boolean value used to prevent recurrsion when protecting user privacy by limiting the location accuracy
		 * @return array containing (coordinates, 
		 */
		function getGeoCoords($query, $simple_query = false) {
			
			if ($this->checkGoogleAPIKey()) {
				//Sanitize user input
				$clean_query = urlencode(strip_tags($query));
				
				$request = 'http://maps.google.com/maps/geo?q=' . $clean_query . '&output=xml&oe=utf8&sensor=false&key=' . $this->google_api_key;
				$full_response = new SimpleXMLElement($request, NULL, TRUE);
				$response = $full_response->Response;
				if ($this->validLocation($response)) {
					// Protect users privacy by limiting the location accuracy to City, State level
					$address_attribs = $response->Placemark[0]->AddressDetails->attributes();
					foreach ($address_attribs as $attrib_name => $attrib_value) {
						if ($attrib_name == 'Accuracy') {
							$address_accuracy = $attrib_value;
						}
					}
					
					if ($address_accuracy > 5 && $simple_query === false) {
						$country_details = $response->Placemark[0]->AddressDetails->Country;
						if (!empty($country_details->AdministrativeArea->SubAdministrativeArea)) {
							$city = $country_details->AdministrativeArea->SubAdministrativeArea->Locality->LocalityName;
							$postal_code = $country_details->AdministrativeArea->SubAdministrativeArea->Locality->PostalCode->PostalCodeNumber;
						}
						else {
							$city = $country_details->AdministrativeArea->Locality->LocalityName;
							$postal_code = $country_details->AdministrativeArea->Locality->PostalCode->PostalCodeNumber;
						}
						$state = $country_details->AdministrativeArea->AdministrativeAreaName;
						$country = $country_details->CountryName;
						
						$simplified_query = '';
						if (!empty($city)) {
							$simplified_query .= $city . ',';
						}
						if (!empty($state)) {
							$simplified_query .= ' ' . $state;
						}
						if (!empty($postal_code)) {
							$simplified_query .= ' ' . $postal_code . ',';
						}
						if (!empty($country)) {
							$simplified_query .= ' ' . $country;
						}
						
						return $this->getGeoCoords($simplified_query,true);
					}
					
					$coords = $response->Placemark[0]->Point->coordinates;
					$simplified_location = $response->Placemark[0]->address;
					return array($coords,$simplified_location);
				}
			}
			
			return false;
		}
		
		/**
		 * Check to see Google geo code response and see if it found a match
		 */
		function validLocation($response) {
			
			// Let's first make sure this is a valid response
			$code = $response->Status->code;
			
			if ($code == 200) {
				// It is a valid response, now we need to see if only one placemark was returned
				$placemarks = $response->Placemark;
				
				if (count($placemarks) == 1) {
					// If one placemark was returned, we got it!
					return true;
				}
				else {
					// If multiple placemarks were turned, send an error
					$this->error = TTB_ERROR_602;
				}
			}
			else if ($code == 500) {
				$this->error = TTB_ERROR_500;
			}
			else if ($code == 601) {
				$this->error = TTB_ERROR_601;
			}
			else if ($code == 602) {
				$this->error = TTB_ERROR_602;
			}
			else if ($code == 603) {
				$this->error = TTB_ERROR_603;
			}
			else if ($code == 610) {
				$this->error = TTB_ERROR_610;
			}
			else if ($code == 620) {
				$this->error = TTB_ERROR_620;
			}
			
			return false;

		}
		
		/**
		 * Randomize the last 3 digits of the Latitude and Longitude coordinates.
		 * This prevents identical coordinates from stacking directly on top of each other.
		 * 
		 * FIXME: It is possible that this function could randomly generate another set of identical coordinates
		 */
		function randomizeCoords($coords) {
			$split_coords = split(',',$coords);
			
			$lat = $split_coords[0];
			$random_lat = substr_replace($lat,rand(100,999),(strlen($lat)-3));
			
			$lon = $split_coords[1];
			$random_lon = substr_replace($lon,rand(100,999),(strlen($lon)-3));
			
			$random_coords = $random_lat . ',' . $random_lon . ',' . $split_coords[2];
			
			return $random_coords;
		}
		
		/**
		 * Used by the getRealIPAddress function, to test if an IP Address is valid
		 */
		function validIPAddress($ip) {

			if (!empty($ip) && ip2long($ip)!=-1) {
				$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
				);

				foreach ($reserved_ips as $r) {
					$min = ip2long($r[0]);
					$max = ip2long($r[1]);
					if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) 
						return false;
				}

				return true;

			} else {
				return false;
			}
			
		}

		/**
		 * Returns the visitors real IP address
		 */
		function getRealIPAddress() {

			if ($this->validIPAddress($_SERVER["HTTP_CLIENT_IP"])) {
				return $_SERVER["HTTP_CLIENT_IP"];
			}

			foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
				if ($this->validIPAddress(trim($ip))) {
					return $ip;
				}
			}

			if ($this->validIPAddress($_SERVER["HTTP_X_FORWARDED"])) {
				return $_SERVER["HTTP_X_FORWARDED"];
			} elseif ($this->validIPAddress($_SERVER["HTTP_FORWARDED_FOR"])) {
				return $_SERVER["HTTP_FORWARDED_FOR"];
			} elseif ($this->validIPAddress($_SERVER["HTTP_FORWARDED"])) {
				return $_SERVER["HTTP_FORWARDED"];
			} elseif ($this->validIPAddress($_SERVER["HTTP_X_FORWARDED"])) {
				return $_SERVER["HTTP_X_FORWARDED"];
			} else {
				return $_SERVER["REMOTE_ADDR"];
			}
		}
		
		/**
		 * Check Google API Key
		 */
		function checkGoogleAPIKey() {
			
			if (!empty($this->google_api_key) && isset($this->google_api_key)) {
				return true;
			}
			
			return false;
			
		}
		
	}
} //End Class TrackTheBook

// Initialize objects
$trackthebook = new TrackTheBook();
$trackthebook_model = new TrackTheBookModel();
$trackthebook_admin = new TrackTheBookAdmin();
$trackthebook_view = new TrackTheBookView();

// Include require javascript files
wp_enqueue_style('thickbox');
wp_enqueue_script('jquery');
wp_enqueue_script('thickbox');
wp_enqueue_script('jquery-form');
wp_enqueue_script('jquery-validate', TTB_URL.'/js/jquery.validate.min.js', array('jquery'));

// Create custom views and ajax actions
if($_GET['view'] === 'trackthebook_form'){
	$trackthebook_view->render('register-form');
	exit();
}
else if($_GET['view'] === 'trackthebook.kml'){
	$trackthebook_view->render('trackthebook-kml');
	exit();
}
else if($_GET['action'] === 'get_location'){
	$geo_data = $trackthebook->getGeoCoords($_GET['location']);
	if (!empty($geo_data)) {
		$trackthebook_view->renderJSON(array('status' => '200', 'coords' => $geo_data[0], 'simplified_location' => $geo_data[1]));
	}
	else {
		$trackthebook_view->renderJSON(array('status'  => '406', 'message' => $trackthebook->error));
	}
	exit();
}
else if($_GET['action'] === 'register_my_book'){
	$book = $_GET['book'];
	$location = $_GET['simplified_location'];
	$coords = $_GET['coords'];
	$name = $_GET['name'];
	$email = $_GET['email'];
	$school = $_GET['school'];
	$grade = $_GET['grade'];
	
	// Double-check for missing fields
	if (empty($book) || empty($coords)) {
		$trackthebook_view->renderJSON(array('status' => '406','message' => TTB_ERROR_MISSING));
	}
	else {
		if (!empty($trackthebook->randomize_identical_coords)) {
			if (!$trackthebook_model->uniqueCoords($coords)) {
				$coords = $trackthebook->randomizeCoords($coords);
			}
		}
		$trackthebook_model->insertBook($book, $name, $location, $coords, $email, $school, $grade);
		$trackthebook_view->renderJSON(array('status' => '200','message' => TTB_SUCCESS));
	}
	exit();
}

// Hooks
register_activation_hook(__FILE__,array($trackthebook_model,'install'));

// Add Shortcodes
add_shortcode( 'trackthebook', array($trackthebook_view,'shortcodeRegisterLink') );

// Admin Hooks
add_action('admin_menu', array($trackthebook_admin,'adminMenu'));
add_filter('plugin_row_meta',array($trackthebook_admin,'pluginShortcuts') ,10,2);

// Dashboard
add_action('wp_dashboard_setup', array($trackthebook_admin,'addDashboardWidgets'));

?>