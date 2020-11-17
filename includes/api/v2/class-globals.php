<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
        * @package tindapress-wp-plugin
		* @version 0.2.0
		* This is the primary gateway of all the rest api request.
    */

  	class TP_Globals_v2 {

        /**
		 * GENERATING PUBLICKEY
		 * @param primary_key = primary key
		 * @param table_name = table name
		 * @param column_name = Column name to be updated
		 */
		public static function generating_pubkey($primary_key, $table_name, $column_name, $get_key, $lenght){
            global $wpdb;

            $sql = "UPDATE  $table_name SET $column_name = concat(
                substring('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', rand(@seed:=round(rand($primary_key)*4294967296))*36+1, 1), ";

            for ($i=0; $i < $lenght ; $i++) {
                $sql .= "substring('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),";
            }

            $sql .=" substring('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', rand(@seed)*36+1, 1)
            )
            WHERE ID = $primary_key;";


            $results = $wpdb->query($sql);

            if ($get_key = true) {
                $key  = $wpdb->get_row("SELECT `$column_name` as `key` FROM $table_name WHERE ID = '$primary_key' ");
                return $key->key;
            }

            if ($results < 1) {
				return false;
			}else{
				if ($results == 1) {
					return true;
				}
			}
        }

        public static function check_listener($array_post){
			$var = array();
			$keys = array();

			foreach ($array_post as $key => $value) {
				$var[] = $value;
				$keys[] = $key;
			}

			for ($count=0; $count < count($var) ; $count++) {
				if (empty($var[$count])) {
					return $keys[$count];
				}
			}
			return true;

		}

        public static function verify_prerequisites(){

            if(!class_exists('DV_Verification') ){
                return 'DataVice';
            }
            return true;
		}

		public static function date_stamp(){
            return date("Y-m-d h:i:s");
		}

        public static function upload_image($request, $files){
			$data = array();
			foreach ($files as $key => $value) {

				$max_img_size = DV_Library_Config::dv_get_config('max_img_size', 123);
				if (!$max_img_size) {
					return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Can't find config of img size.",
					);
				}

				//Get the directory of uploading folder
				$target_dir = wp_upload_dir();

				//Get the file extension of the uploaded image
				$file_type = strtolower(pathinfo($target_dir['path'] . '/' . basename($files[$key]['name']),PATHINFO_EXTENSION));

				if (!isset($_POST['IN'])) {
					$img_name = $files[$key]['name'];

				} else {
					$img_name = sanitize_file_name($_POST['IN']);

				}

				$completed_file_name = sha1(date("Y-m-d~h:i:s"))."-". trim($img_name," ");

				$target_file = $target_dir['path'] . '/' . basename($completed_file_name);
				$uploadOk = 1;

				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

				$check = getimagesize($files[$key]['tmp_name']);

				if($check !== false) {
					$uploadOk = 1;

				} else {
					$uploadOk = 0;
					return array(
						"status" => "failed",
						"message" => "File is not an image.",
					);
				}

				// Check if file already exists
				if (file_exists($target_file)) {
					//  file already exists
					$uploadOk = 0;
					return array(
						"status" => "failed",
						"message" => "File is already existed.",
					);
				}

				// Check file size
				if ($files[$key]['size'] > $max_img_size) {
					// file is too large
					$uploadOk = 0;
					return array(
						"status" => "failed",
						"message" => "File is too large.",
					);
				}

				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType !=
					"jpeg"
					&& $imageFileType != "gif" ) {
					//only JPG, JPEG, PNG & GIF files are allowed
					$uploadOk = 0;
					return array(
						"status" => "failed",
						"message" => "Only JPG, JPEG, PNG & GIF files are allowed.",
					);
				}

				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
				// file was not uploaded.
					// if everything is ok, try to upload file
						return array(
							"status" => "unknown",
							"message" => "Please contact your admnistrator. File has not uploaded! ",
						);

				} else {//

					if (move_uploaded_file($files[$key]['tmp_name'], $target_file)) {

						$pic = $files[$key];
						$file_mime = mime_content_type( $target_file);

						$upload_id = wp_insert_attachment( array(
							'guid'           => $target_file,
							'post_mime_type' => $file_mime,
							'post_title'     => preg_replace( '/\.[^.]+$/', '', $pic['name'] ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						), $target_file );

						// wp_generate_attachment_metadata() won't work if you do not include this file
						require_once( ABSPATH . 'wp-admin/includes/image.php' );

						$attach_data = wp_generate_attachment_metadata( $upload_id, $target_file );

						// Generate and save the attachment metas into the database
						wp_update_attachment_metadata( $upload_id, $attach_data );

						// Show the uploaded file in browser
						wp_redirect( $target_dir['url'] . '/' . basename( $target_file ) );

						//return file path
						$data[$key] = (string)$target_dir['url'].'/'.basename($completed_file_name);
						$data[$key.'_id'] = $upload_id;


					} else {
						//there was an error uploading your file
						return array(
							"status" => "unknown",
							"message" => "Please contact your admnistrator. File has not uploaded! ",
						);
					}
				}
				// End
			}
			// End loop

			return array(
				"status" => "success",
				"data" => array($data)

			);
		}

		public static function check_type($type, $values = array()){
			global $wpdb;
			#return $values;

			for ($i=0; $i < count($values) ; $i++) {

				if($type == $values[$i]->title){
					return true;
				}
			}
			return false;
		}

		public static function seen($table_name, $wpid, $unique_column, $unique_column_id){
			global $wpdb;

			// Check if this user already seen this unique column
				$check_unique = $wpdb->get_row("SELECT `ID` FROM $table_name WHERE `wpid` = '$wpid' AND `$unique_column` = '$unique_column_id' ");
				if (!empty($check_unique)) {
					return true;
				}else{

					// Import seen

						$import_unique_seen = $wpdb->query("INSERT INTO $table_name ( `wpid`, `$unique_column` ) VALUES ( '$wpid', '$unique_column_id' ) ");
						$import_unique_seen_id = $wpdb->insert_id;
						$import_unique_seen_hsid = $wpdb->query("UPDATE $table_name SET hsid = sha2($import_unique_seen_id, 256) WHERE ID = '$import_unique_seen_id' ");
					// End
					if ($import_unique_seen < 1 || $import_unique_seen_hsid < 1 ) {
						return false;
					}else{
						return true;
					}
				}
			// End
		}
    }