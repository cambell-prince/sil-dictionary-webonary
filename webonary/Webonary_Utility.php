<?php
/** @noinspection SqlResolve */

class Webonary_Utility
{
	// Receive upload. Unzip it to uploadPath. Remove upload file.
	public static function unzip($zipfile, $uploadPath, $zipFolderPath)
	{
		if (!function_exists('wp_handle_upload'))
		{
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
		}

		$overrides = array( 'test_form' => false, 'test_type' => false );
		$file = wp_handle_upload($zipfile, $overrides);

		if (isset( $file['error']))
		{
			echo "Error: Upload failed: " . $file['error'] . "\n";
			unlink($uploadPath . "/" . $zipfile['name']);
			return false;
		}

		error_log("zip file: " . $uploadPath . "/" . $_FILES['file']['name']);
		error_log(WP_CONTENT_DIR . "/archives");

		if(file_exists(WP_CONTENT_DIR . "/archives"))
		{
			error_log("copy zip file");
			copy($uploadPath . "/" . $_FILES['file']['name'], WP_CONTENT_DIR . "/archives/" . $_FILES['file']['name']);
		}

		$zip = new ZipArchive;
		$res = $zip->open($uploadPath . "/" . $zipfile['name']);
		if ($res === FALSE)
		{
			echo "Error: " . $zipfile['name'] . " isn't a valid zip file";
			unlink($uploadPath . "/" . $zipfile['name']);
			return false;
		}

		$unzip_success = $zip->extractTo($zipFolderPath);
		$zip->close();
		if(!$unzip_success)
		{
			echo "Error: couldn't extract zip file to " . $uploadPath;
			unlink($uploadPath . "/" . $zipfile['name']);
			return false;
		}

		error_log("zip file extracted");
		echo "zip file extracted successfully\n";
		unlink($uploadPath . "/" . $zipfile['name']);
		return true;
	}

	// Function to remove folders and files
	public static function recursiveRemoveDir($dir)
	{
		if (is_dir($dir)) {
			$files = scandir($dir);
			foreach ($files as $file)
			{
				if ($file != "." && $file != "..") self::recursiveRemoveDir("$dir/$file");
				error_log("removed: " . $dir);
				self::deleteDirectory($dir);
			}
		}
		else if (file_exists($dir)) unlink($dir);
	}

	private static function deleteDirectory($dir)
	{
		if (!file_exists($dir)) {
			return true;
		}

		if (!is_dir($dir)) {
			return unlink($dir);
		}

		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}

			if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
				return false;
			}

		}

		return rmdir($dir);
	}

	// Function to Copy folders and files
	public static function recursiveCopy($src, $dst)
	{
		if (is_dir ( $src )) {
			if(!file_exists($dst))
			{
				error_log("create folder: " . $dst);
				mkdir ( $dst, 0777, true );
			}
			$files = scandir ( $src );
			foreach ( $files as $file )
				if ($file != "." && $file != "..")
					self::recursiveCopy ( "$src/$file", "$dst/$file" );
		} else if (file_exists ( $src )) {
			copy ( $src, $dst );
			error_log("moved: " . $src . " to " . $dst);
		}
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function resize_image($src, $w, $h, $dst)
	{
		if(!file_exists($dst))
		{
			mkdir ( $dst );
		}
		$files = scandir ( $src );
		foreach ( $files as $file )
		{
			if ($file != "." && $file != "..")
			{
				list($width, $height) = getimagesize($src . "/"  . $file);

				echo $width . "#" . $height . "\n";

				$r = $width / $height;
				$newwidth = $h*$r;
				$newheight = $h;

				if($newheight <= $height && $newwidth <= $width)
				{
					$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

					try {
						if($ext == "png")
						{
							$src_image = imagecreatefrompng($src . "/" . $file);
						}
						elseif($ext == "gif")
						{
							$src_image = imagecreatefromgif($src . "/" . $file);
						}
						else
						{
							$src_image = imagecreatefromjpeg($src . "/" . $file);
						}
						$dst_image  = imagecreatetruecolor($newwidth, $newheight);
						imagecopyresized($dst_image, $src_image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );

						if($ext == "png")
						{
							imagepng($dst_image, $dst . "/" . $file);
						}
						elseif($ext == "gif")
						{
							imagegif($dst_image, $dst . "/" . $file);
						}
						else
						{
							imagejpeg($dst_image, $dst . "/" . $file, 90);
						}
					}
					catch(Exception $e) {
						echo 'There was an error converting image file to thumbnail: ' .$e->getMessage();
					}
				}
				else
				{
					copy ( $src . "/" . $file, $dst . "/" . $file );
				}
				echo $dst . "/" . $file . "\n";
			}
		}
	}

	public static function verifyAdminPrivileges($username, $password)
	{
		global $wpdb;

		if(!empty($username))
		{
			$user = wp_authenticate($username, $password );
		}
		else
		{
			$user = wp_authenticate( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );
		}


		if(isset($user->ID))
		{
			$sql = "SELECT meta_value AS userrole FROM wp_usermeta " .
				" WHERE (user_id = " . $user->ID . " AND meta_key = 'wp_" . get_current_blog_id()  . "_capabilities') OR " .
				" (user_id = " . $user->ID . " AND meta_key = 'wp_capabilities')";


			$roleSerialized = $wpdb->get_var($sql);
			$user_role = unserialize($roleSerialized);

			if(!empty($user_role['editor']) || !empty($user_role['administrator']))
			{
				return get_userdata($user->ID);
			}
			else
			{
				echo "User doesn't have permission to import data to this Webonary site\n";
				return false;
			}

		}
		else
		{
			echo "Wrong username or password.";
			return false;
		}
	}

	public static function includeTemplate($template_name, $substitutions)
	{
		global $webonary_template_path;

		$html = file_get_contents($webonary_template_path . DIRECTORY_SEPARATOR . $template_name);

		if (empty($substitutions))
			return $html;

		foreach($substitutions as $key => $value) {
			$html = str_replace($key, $value, $html);
		}

		return $html;
	}

	/**
	 * Executes $function, sends the response to the browser, closes the connection, and continues processing.
	 * @param Closure $function
	 */
	public static function sendAndContinue($function)
	{
		ignore_user_abort(true);
		set_time_limit(0);

		ob_start();

		$function();

		// set the response code to 200, if not already set
		if (http_response_code() === false)
			http_response_code(200);

		header('Connection: close');
		header('Content-Length: ' . ob_get_length());
		ob_end_flush();
		ob_flush();
		flush();
	}

	/**
	 * This function attempts to remove everything from the output buffers.
	 * Returns FALSE if the headers have already been sent.
	 *
	 * @return bool
	 */
	public static function clearResponse()
	{
		$headers_sent = headers_sent();

		// attempt to remove all headers
		if (!$headers_sent)
			header_remove();

		// discard all the buffered output
		while (ob_get_level()) {
			ob_end_clean();
		}

		return !$headers_sent;
	}

	public static function disablePlugins($plugins)
	{
		$key = array_search('qtranslate-x/qtranslate.php' , $plugins);
		if (false !== $key)
			unset($plugins[$key]);

		return $plugins;
	}
}
