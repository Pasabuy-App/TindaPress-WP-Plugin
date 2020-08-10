<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) 
	{
		exit;
	}

	/** 
        * @package tindapress-wp-plugin
        * @version 0.1.0
	*/
    class TP_Logo_update{
       
        // image upload
        public static function listen(WP_REST_Request $request) {
    
            global $wpdb;
            $later = TP_Globals::date_stamp();

            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_store_fields = TP_STORES_FIELDS;

            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;

            $revs_type = "stores";
            $wpid = $_POST["wpid"];
            $stid = $_POST["stid"];

            //Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }
            
            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                    )
                );
            }

           
            $files = $request->get_file_params();
            
            // Step3 : Sanitize all Request
            if ( !isset($files['img']) || !isset($_POST['stid'])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request Unknown!",
					)
				);
            }
            

            // Step5 : sanitize if all variables is empty
            if ( empty($_POST["stid"]) ){
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                    )
                );
            }
            
            // Step6 : Validation of store
            $get_store = $wpdb->get_row("SELECT ID FROM $table_store  WHERE ID = $stid  ");
                
             if ( !$get_store ) {
                return rest_ensure_response( 
                    array(
                        "status" => "error",
                        "message" => "No store found.",
                    )
                );
			}

            // Step7 : sanitize if all variables is empty
            if ( $files['img']['name'] == NULL  || $files['img']['type'] == NULL) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please select an image!",
					)
				);
            }
            
            //Get the directory of uploading folder
            $target_dir = wp_upload_dir();

            //Get the file extension of the uploaded image
            $file_type = strtolower(pathinfo($target_dir['path'] . '/' . basename($files['img']['name']),PATHINFO_EXTENSION));
            //Optional of picture name
            if (!isset($_POST['in'])) {
                $img_name = $files['img']['name'];
            } else {
                $img_name = sanitize_file_name($_POST['in']);
            }

            //Image name complete
            $completed_file_name = 'Logo-'.$img_name;

            //Target path to move the file
            $target_file = $target_dir['path'] . '/' . basename($completed_file_name);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            $check = getimagesize($files['img']['tmp_name']);
            
            if($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Invalid file type. Only image are allowed.",
					)
				);
            }
            // Check if file already exists
            if (file_exists($target_file)) {
                $uploadOk = 0;
                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "A file with this name already exists",
					)
				);
            }
            // Check file size
            if ($files['img']['size'] > 500000) {
                $uploadOk = 0;
                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Your image file size was too big.",
					)
				);
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
                "jpeg"
            && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Invalid image file type. JPG, PNG, JPEG and GIF types are only accepted",
					)
				);
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                return rest_ensure_response( 
					array(
						"status" => "error",
						"message" => "An error occured while submitting data to the server.",
					)
				);
            } else {

                $var = $target_dir['path'];

                //Logo Name
                $logo_name = trailingslashit($target_dir['subdir']).$completed_file_name;

                if (move_uploaded_file($files['img']['tmp_name'], $target_file)) {
              
                // Query
                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '$stid', 'logo', '$logo_name', '$wpid', '$later')");
                $logo_id = $wpdb->insert_id;
                $result = $wpdb->query("UPDATE $table_store SET `logo` = $logo_id WHERE ID = '$stid' ");

                    return rest_ensure_response( 
                        array(
                            "status" => "success",
                            "message" => "Data has been updated successfully.",
                        )
                    );  
               
                } else {
                    return rest_ensure_response( 
                        array(
                            "status" => "error",
                            "message" => "An error occured while submitting data to the server.",
                        )
                    );
                }
            }


		}

    }
