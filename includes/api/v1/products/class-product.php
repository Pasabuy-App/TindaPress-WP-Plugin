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
?>
<?php

    class TP_Products {

        public static function add_product(){
            global $wpdb;
            
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            // Step1 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step2 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST["ctid"]) || !isset($_POST["stid"]) || !isset($_POST["title"]) || !isset($_POST["short_info"]) || !isset($_POST["long_info"]) ||  !isset($_POST["sku"]) ||   !isset($_POST["price"]) || !isset($_POST["weight"]) || !isset($_POST["dimension"]) || !isset($_POST["preview"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 3: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"]) || !is_numeric($_POST["stid"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 4: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            $later = TP_Globals::date_stamp();

            $created_by = $_POST['wpid'];
            $ctid = $_POST['ctid'];
            $stid = $_POST['stid'];

            $table_product = TP_PRODUCT_TABLE;
            $table_revs = TP_REVISION;

            
            $child_vals = array(
                $_POST['title'],
                $_POST['short_info'],
                $_POST['long_info'],
                $_POST['sku'],
                $_POST['price'],
                $_POST['weight'],
                $_POST['dimension'],
                $_POST['preview'],
            );

            $revs_type = "products";

            $child_keys = array('title', 'short_info', 'long_info', 'sku', 'price',  'weight',  'dimension', 'preview' );
            $last_id_product = array();

            for ($count=0; $count < count($child_keys) ; $count++) {
                $sql = "INSERT INTO $table_revs (revs_type, child_key , child_val, created_by, date_created  ) VALUES ('$revs_type', '$child_keys[$count]', '$child_vals[$count]', $created_by, '$later')";
                $result = $wpdb->query($sql );
                $last_id=$wpdb->insert_id;
                $last_ids[] = $last_id;

            }


            $vals  = implode(", ", $last_ids);
            $colss  = implode(", ", $child_keys); 
            $sql2 = "INSERT INTO $table_product (stid, ctid, $colss, created_by, date_created) VALUES ($stid, $ctid, $vals, $created_by, '$later' )";
            $result_update = $wpdb->query($sql2);


            $last_product_id = $wpdb->insert_id;


            for ($count=0; $count < count($child_keys) ; $count++) { 
                $resultss = $wpdb->update($table_revs, array('parent_id' => $last_product_id), array( 'ID' => $last_ids[$count]));
            }

            return rest_ensure_response( 
                array(
                    "status" => "success",
                    "message" => "Product added successfully!",
                )
            );

        }

        public static function get_product(){
            global $wpdb;


            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }


            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Please contact your administrator. Plugin Missing",
                    )
                );
            }

            // Step1 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step2 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 3: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"])  ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 4: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            $table_product = TP_PRODUCT_TABLE;
            $table_product_revs = TP_PRODUCT_REVS_TABLE;
            $table_stores = TP_STORES_TABLE;
            $table_stores_revs = TP_STORES_REVS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_categories_revs = TP_CATEGORIES_REVS_TABLE;

            $table_revs = TP_REVISION;
            if(!isset($_POST['lid'])){

                $result =  $wpdb->get_results("SELECT
                        prod.id AS id,
                        revs_2.child_val AS store_name,
                        max( IF ( revs_2.child_key = 'title', revs_2.child_val, '' ) ) AS cat_title,
                        max( IF ( revs_2.child_key = 'info', revs_2.child_val, '' ) ) AS cat_info,
                        max( IF ( revs.child_key = 'title', revs.child_val, '' ) ) AS title,
                        max( IF ( revs.child_key = 'preview', revs.child_val, '' ) ) AS preview,
                        max( IF ( revs.child_key = 'short_info', revs.child_val, '' ) ) AS short_info,
                        max( IF ( revs.child_key = 'long_info', revs.child_val, '' ) ) AS long_info,
                        max( IF ( revs.child_key = 'status', revs.child_val, '' ) ) AS STATUS,
                        max( IF ( revs.child_key = 'sku', revs.child_val, '' ) ) AS sku,
                        max( IF ( revs.child_key = 'price', revs.child_val, '' ) ) AS price,
                        max( IF ( revs.child_key = 'weight', revs.child_val, '' ) ) AS weight,
                        max( IF ( revs.child_key = 'dimension', revs.child_val, '' ) ) AS dimension,
                        revs.created_by,
                        prod.date_created 
                    FROM
                        $table_product prod
                        INNER JOIN $table_revs revs ON prod.title = revs.ID 
                        OR prod.preview = revs.ID 
                        OR prod.short_info = revs.ID 
                        OR prod.long_info = revs.ID 
                        OR prod.`status` = revs.ID 
                        OR prod.sku = revs.ID 
                        OR prod.price = revs.ID 
                        OR prod.weight = revs.ID 
                        OR prod.dimension = revs.ID
                        INNER JOIN $table_stores str ON prod.stid = str.ID
                        INNER JOIN $table_revs revs_1 ON str.title = revs_1.ID
                        INNER JOIN $table_categories cat ON prod.ctid = cat.ID
                        INNER JOIN $table_revs revs_2 ON cat.title = revs_2.ID 
                        OR cat.info = revs_2.ID 
                    GROUP BY
                        revs.parent_id DESC LIMIT 12
                ");
                $last_id = min($result);

                //Step 6: Return result
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                            'last_id' => $last_id
                        )
                    )
                );

            }else{

                if(!is_numeric($_POST["lid"])){
					return rest_ensure_response( 
						array(
							"status" => "failed",
							"message" => "Parameters not in valid format!",
						)
					);

                }
                
                $get_last_id = $_POST['lid'];
                $add_feeds = $get_last_id - 5;


                
                $result =  $wpdb->get_results("SELECT
                prod.id AS id,
                revs_2.child_val AS store_name,
                max( IF ( revs_2.child_key = 'title', revs_2.child_val, '' ) ) AS cat_title,
                max( IF ( revs_2.child_key = 'info', revs_2.child_val, '' ) ) AS cat_info,
                max( IF ( revs.child_key = 'title', revs.child_val, '' ) ) AS title,
                max( IF ( revs.child_key = 'preview', revs.child_val, '' ) ) AS preview,
                max( IF ( revs.child_key = 'short_info', revs.child_val, '' ) ) AS short_info,
                max( IF ( revs.child_key = 'long_info', revs.child_val, '' ) ) AS long_info,
                max( IF ( revs.child_key = 'status', revs.child_val, '' ) ) AS STATUS,
                max( IF ( revs.child_key = 'sku', revs.child_val, '' ) ) AS sku,
                max( IF ( revs.child_key = 'price', revs.child_val, '' ) ) AS price,
                max( IF ( revs.child_key = 'weight', revs.child_val, '' ) ) AS weight,
                max( IF ( revs.child_key = 'dimension', revs.child_val, '' ) ) AS dimension,
                revs.created_by,
                prod.date_created 
            FROM
                $table_product prod
                INNER JOIN $table_revs revs ON prod.title = revs.ID 
                OR prod.preview = revs.ID 
                OR prod.short_info = revs.ID 
                OR prod.long_info = revs.ID 
                OR prod.`status` = revs.ID 
                OR prod.sku = revs.ID 
                OR prod.price = revs.ID 
                OR prod.weight = revs.ID 
                OR prod.dimension = revs.ID
                INNER JOIN $table_stores str ON prod.stid = str.ID
                INNER JOIN $table_revs revs_1 ON str.title = revs_1.ID
                INNER JOIN $table_categories cat ON prod.ctid = cat.ID
                INNER JOIN $table_revs revs_2 ON cat.title = revs_2.ID 
                OR cat.info = revs_2.ID 
                WHERE prod.id BETWEEN $add_feeds AND ($get_last_id - 1) 
            GROUP BY
                revs.parent_id DESC LIMIT 12
                
              
                ");

                //Step 4: Check if array count is 0 , return error message if true
				if (count($result) < 1) {

					return rest_ensure_response( 
						array(
							"status" => "failed",
							"message" => "No more posts to see",
						)
                    );
                    
				} else {

					//Pass the last id
                    $last_id = min($result);
                    //Step 5: Return a success message and a complete object
                    return rest_ensure_response( 
                        array(
                            "status" => "success",
                            "data" => array(
                                'list' => $result, 
                                'last_id' => $last_id
                            )
                        )
                    );
                    
                }
                
            }
        }

        public static function update_product(){
            global $wpdb;

            
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            // Step1 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step2 : Sanitize all Request
            if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST["ctid"]) || !isset($_POST['pdid']) || !isset($_POST["stid"]) || !isset($_POST["title"]) || !isset($_POST["short_info"]) || !isset($_POST["long_info"]) ||  !isset($_POST["sku"]) ||   !isset($_POST["price"]) || !isset($_POST["weight"]) || !isset($_POST["dimension"]) || !isset($_POST["preview"])) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                    )
                );
                
            }

            // Step 3: Check if ID is in valid format (integer)
            if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"]) || !is_numeric($_POST["stid"]) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Please contact your administrator. ID not in valid format!",
                    )
                );
                
            }

            // Step 4: Check if ID exists
            if (!get_user_by("ID", $_POST['wpid'])) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "User not found!",
                    )
                );
                
            }

            $now = current_time( 'mysql' ); 
            $later = TP_Globals::date_stamp();
            $created_by = $_POST['wpid'];
            $parent_id = $_POST['pdid'];
            $ctid = $_POST['ctid'];
            $stid = $_POST['stid'];
            $revs_type = "products";

            $child_vals = array(
                $_POST['title'],
                $_POST['short_info'],
                $_POST['long_info'],
                $_POST['sku'],
                $_POST['price'],
                $_POST['weight'],
                $_POST['dimension'],
                $_POST['preview'],
            );

            
            $child_keys = array('title', 'short_info', 'long_info', 'sku', 'price',  'weight',  'dimension', 'preview' );
            $last_id_product = array();

            $table_revs = TP_REVISION;
            $table_product = TP_PRODUCT_TABLE;


            for ($count=0; $count < count($child_keys) ; $count++) {
                $sql = "INSERT INTO $table_revs (revs_type, parent_id, child_key , child_val, created_by, date_created  ) VALUES ('$revs_type', $parent_id, '$child_keys[$count]', '$child_vals[$count]', '$created_by', '$later')";
                // $result = $wpdb->insert('$table_product_revs', array( 'parent_id'=> $parent_id, 'child_key'=> $child_keys[$count], 'child_val' => $child_vals[$count] , 'created_by' => $created_by, 'date_created' => $later ) );
                $result_insert = $wpdb->query($sql);
                $last_id=$wpdb->insert_id;
                $last_ids[] = $last_id;

                $sql2 = "UPDATE $table_product SET  $child_keys[$count] = $last_ids[$count] WHERE ID= $parent_id ";
                $update_result = $wpdb->query($sql2);

            }

            if($result_insert < 0 || $update_result <0 ){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator!",
                    )
                );

            }else {
				return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "message" => "Product has been updated successfully!",
                    )
                );
                
			}

        }

        public static function retrieveById_product(){
            global $wpdb;
                
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            // Step1 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step2 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 3: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"])  ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 4: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }
            $table_product = TP_PRODUCT_TABLE;
            $table_product_revs = TP_PRODUCT_REVS_TABLE;
            $table_stores = TP_STORES_TABLE;
            $table_stores_revs = TP_STORES_REVS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_categories_revs = TP_CATEGORIES_REVS_TABLE;

            $table_revs = TP_REVISION;
            $pdid = $_POST['pdid'];

            $result =  $wpdb->get_results("SELECT
                prod.id AS id,
                revs_2.child_val AS store_name,
                max( IF ( revs_2.child_key = 'title', revs_2.child_val, '' ) ) AS cat_title,
                max( IF ( revs_2.child_key = 'info', revs_2.child_val, '' ) ) AS cat_info,
                max( IF ( revs.child_key = 'title', revs.child_val, '' ) ) AS title,
                max( IF ( revs.child_key = 'preview', revs.child_val, '' ) ) AS preview,
                max( IF ( revs.child_key = 'short_info', revs.child_val, '' ) ) AS short_info,
                max( IF ( revs.child_key = 'long_info', revs.child_val, '' ) ) AS long_info,
                max( IF ( revs.child_key = 'status', revs.child_val, '' ) ) AS STATUS,
                max( IF ( revs.child_key = 'sku', revs.child_val, '' ) ) AS sku,
                max( IF ( revs.child_key = 'price', revs.child_val, '' ) ) AS price,
                max( IF ( revs.child_key = 'weight', revs.child_val, '' ) ) AS weight,
                max( IF ( revs.child_key = 'dimension', revs.child_val, '' ) ) AS dimension,
                revs.created_by,
                prod.date_created 
            FROM
                $table_product prod
                INNER JOIN $table_revs revs ON prod.title = revs.ID 
                OR prod.preview = revs.ID 
                OR prod.short_info = revs.ID 
                OR prod.long_info = revs.ID 
                OR prod.`status` = revs.ID 
                OR prod.sku = revs.ID 
                OR prod.price = revs.ID 
                OR prod.weight = revs.ID 
                OR prod.dimension = revs.ID
                INNER JOIN $table_stores str ON prod.stid = str.ID
                INNER JOIN $table_revs revs_1 ON str.title = revs_1.ID
                INNER JOIN $table_categories cat ON prod.ctid = cat.ID
                INNER JOIN $table_revs revs_2 ON cat.title = revs_2.ID 
                OR cat.info = revs_2.ID 
            WHERE prod.id = $pdid
            GROUP BY
                revs.parent_id DESC LIMIT 12
            ");

             //Step 6: Return result
             return rest_ensure_response( 
                array(
                    "status" => "success",
                    "data" => array(
                        'list' => $result, 
                    
                    )
                )
            );
        }

        public static function delete_product(){
            global $wpdb;
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }
            // Step1 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

             // Step2 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST['pid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 3: Check if ID is in valid format (integer)
            if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["pid"]) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Please contact your administrator. ID not in valid format!",
                    )
                );
                
            }

            // Step 4: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            $parentid = $_POST['pid'];

            $wpid = $_POST['wpid'];

            $product_type = "products";

            $date_stamp = TP_Globals::date_stamp();

            $wpdb->query("START TRANSACTION ");

                $result1 = $wpdb->query("INSERT INTO tp_revisions (revs_type, parent_id, child_key, child_val, created_by, date_created) VALUES ('$product_type', $parentid , 'status', 'active', $wpid, '$date_stamp'  )");
               
                $last_id = $wpdb->insert_id;

                $result2 = $wpdb->query("UPDATE tp_products SET tp_products.`status` =  $last_id  WHERE tp_products.ID = $parentid ");

            $wpdb->query("COMMIT");

            if ($result1 || $result2 == true) {

                return rest_ensure_response( 
					array(
						"status" => "success",
						"message" => "Product has been updated successfully",
					)
                );

            }else{
                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. Deletion Failed",
					)
                );

            }

        }



      
    }
