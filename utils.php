<?php

	// Utility functions for the Captain Up WordPress plugin
	// -------------------------------------------------------------------------------
	class CaptainUtils {
		
		// Returns the Captain Up domain URL.
		// 
		// In development, update the `CAPTAIN_UP_DOMAIN` environment variable to point
		// to your local Captain Up app.
		// 
		// @return {String} the Captain Up domain URL
		// 
		public static function get_captain_domain() {
			if (empty($_ENV['CAPTAIN_UP_DOMAIN'])) {
				return 'https://captainup.com';
			} else {
				return $_ENV['CAPTAIN_UP_DOMAIN'];
			}
		}

		// Returns a boolean indicating whether the API key is valid or not. The API
		// key is valid if it consists of exactly 24 hexadecimal characters.
		// 
		// @param api_key - {String} the API key
		// @return {Boolean} whether the API key is valid
		// 
		public static function is_valid_api_key($api_key) {
			return preg_match("/^[0-9A-Fa-f]{24,24}$/", $api_key) == 1;
		}

		// Returns a boolean indicating whether the received `$api_secret` is valid
		// or not. An API secret is valid if consists of 64 hexadecimal characters.
		// 
		// @param api_secret - {String} the API secret
		// @return {Boolean} whether the API secret is valid.
		// 
		public static function is_valid_api_secret($api_secret) {
			return preg_match("/^[0-9A-Fa-f]{64,64}$/", $api_secret) == 1;
		}

		// Gets the app data using the given $api_key.
		// Returns false if failed ($api_key invalid) and json_encode object
		// of data otherwise.


		// Retrieves the app data from the Captain Up API, based on the app's API key.
		// 
		// @param api_key - {String} the app's API key
		// @return {Mixed} returns False if the API key is invalid. Otherwise, it
		// returns the response, decoded from JSON to an associative array.
		// 
		public static function get_app_data($api_key) {
			// Return if the `api_key` is empty or invalid
			if (!self::is_valid_api_key($api_key)) {
				return false;
			}
			// The URL of the API request to retrieve the app data
			$api_endpoint = self::get_captain_domain()."/mechanics/v1/app/".$api_key;
			// Send an HTTP request to retrieve the app data
			$data = wp_remote_retrieve_body(wp_remote_get($api_endpoint));
			# Return the JSON decoded data, or false if the request failed.
			return $data ? json_decode($data) : false;
		}

		// Returns a boolean indicating whether the app passed in `app_data` is on
		// a the free plan in Captain Up or is on one of the paid plans.
		// 
		// @param app_data - {Associative Array} the app data
		// @return {Boolean} whether the app is on the free plan or not.
		// 
		public static function is_free_plan($app_data) {
			return $app_data->data->plan == CaptainPricingPlan::Free;
		}

		// Signs a user object with an API secret.
		// 
		// Example Usage:
		// 
		//   $user = array(
		//     "id" => "guid-in-your-site",
		//     "name" => "Captain Up",
		//     "image" => "https://example.com/user-profile-picture.png",
		//     "empty" => null
		//   );
		//   sign_user('YOUR-API-SECRET', $user);
		//   > "ZDljNmU1NWVhYWUxZGUzZmExNjVlMTc3ODE4YWEwNmNhNjI4NDJkOTdiOTNhNDM0N2JlN
		//      zdmMDAzMjJiMmZkNDczMjA3YWQ2NDMzYTU2NjQ4OWRjYmFkZTg3NDM3ZDRlZjcyYzk0Zm
		//      Y3YzY4ODQzMTgzNDJmN2VmNDA1YTgzYjM"
		// 
		// See: https://captainup.com/help/javascript/user-integration#toc-php-example
		// 
		// @param secret - {String} Your Captain Up API secret
		// @param user - {Associative Array} the user dictionary
		// @return {String} the signed user string
		// 
		public static function sign_user($secret, $user) {
			// Remove all `null`, `undefined`, arrays and objects from the object
			foreach($user as $key => $value) {
				if(is_null($value)) {
					unset($user[$key]);
				}
			}
			// Sort the array alphabetically by the key names
			ksort($user, SORT_LOCALE_STRING);
			// Join each key-value pair with a '='...
			$signed_user = array();
			foreach ($user as $key => $value) {
			  array_push($signed_user, $key."=".$value);
			}
			// and join each pair with '&'
			$signed_user = implode("&", $signed_user);
			// Sign the serialized object with SHA-512 in lowercase hexadecimal digits
			$signed_user = hash_hmac('sha512', $signed_user, $secret);
			// Encode it as base 64
			$signed_user = base64_encode($signed_user);
			// remove new lines and and the '=' part at the end, and return
			return preg_replace('/(\n|=+\n?$)/', '', $signed_user);
		}

		// Return an URL for the user's avatar - it first tries BuddyPress avatar if exists,
		// and if it isn't it will fallback for WordPress built-in avatar using `get_avatar()` method
		// offered by WordPress - returns an HTML snippet. `get_avatar_url` takes care
		// of stripping the wrapping HTML code.
		// 
		// See: http://wordpress.stackexchange.com/q/59442/
		// 
		// @param user_id - {String} the user id we want to retrieve the avatar for
		// @return {String} the URL of the user's avatar
		// 
		function get_avatar_url($user_id) {
			if (function_exists('bp_core_fetch_avatar')) {
				// Tries to fetch a BuddyPress avatar first, it gives back the avatar URL directly.
				return bp_core_fetch_avatar(array('item_id' => $user_id, 'html' => false));
			} else {
				// Fallback to WordPress built-in avarat and remove the wrapping HTML code.
				preg_match("/src='(.*?)'/i", get_avatar($user_id), $matches);
				return $matches[1];
			}
			// If the regex matches nothing, return empty string.
			return '';
		}

	}


	// A helper enumerator of all the Captain Up pricing plans
	abstract class CaptainPricingPlan {
		const Free       = 0;
		const Statrup    = 1;
		const Business   = 2;
		const Enterprise = 3;
	}

