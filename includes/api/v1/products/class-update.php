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

    class TP_Product_Update {

        public static function listen($request){
            return rest_ensure_response( 
                TP_Product_Update:: update_product($request)
            );
        }

        //QA done 2020-08-12 11:01 am
        public static function update_product($request){
            
            // 2nd Initial QA 2020-08-24 6:49 PM - Miguel
            global $wpdb;

            // Variables for Tables
            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $table_product = TP_PRODUCT_TABLE;
            $table_product_fields = TP_PRODUCT_FIELDS;
            $revs_type = "products";
            
            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

			//  Step 2: Validate user
			if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            // Step 3: Check if params are passed
            if (!isset($_POST['pdid']) 
                || !isset($_POST["stid"])
                || !isset($_POST["title"]) 
                || !isset($_POST["short_info"]) 
                || !isset($_POST["long_info"]) 
                || !isset($_POST["sku"]) 
                || !isset($_POST["price"]) 
                || !isset($_POST["weight"]) 
                || !isset($_POST["dimension"])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if params passed are not empty
            if (empty($_POST['pdid']) 
                || empty($_POST["stid"]) 
                || empty($_POST["title"]) 
                || empty($_POST["short_info"]) 
                || empty($_POST["long_info"]) 
                || empty($_POST["sku"]) 
                || empty($_POST["price"]) 
                || empty($_POST["weight"]) 
                || empty($_POST["dimension"])) {
               return array(
                    "status" => "unknown",
                    "message" => "Required fields cannot be empty",
               );
           }

            // Step 5: Check if user has roles_access of can_activate_store or either contributor or editor
            $permission = TP_Globals::verify_role($_POST['wpid'], '0', 'can_update_products' );
            
            if ($permission == true) {
                return array(
                    "status" => "failed",
                    "message" => "Current user has no access in editing products.",
                );
            }
            
            // variables for query    
            $later      = TP_Globals::date_stamp();
            $created_by = $_POST['wpid'];
            $product_id = $_POST['pdid'];
            $stid       = $_POST['stid'];
            $revs_type  = "products";

            // Step 6: Check product if it exists
            $get_product = $wpdb->get_row("SELECT
                    tp_prod.ID, tp_prod.ctid, tp_prod.status as status_id,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.status ) AS `status`
                FROM
                    $table_product tp_prod
                INNER JOIN 
                    $table_revs tp_rev ON tp_rev.ID = tp_prod.title
                WHERE
                    tp_prod.ID = $product_id
                GROUP BY
                    tp_prod.ID
            ");
            
            //Check if no rows found
            if (!$get_product) {
                return array(
                    "status" => "failed",
                    "message" => "This product does not exists",
                );
            }

            //Fails if product is currently inactive
            if ($get_product->status == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This product is currently inactive.",
                );
            }

            $status_id = $get_product->status_id;

            $ctid = "";

            if(isset($_POST['catid'])){

                $cat_id = $_POST['catid'];

                $check_cat = $wpdb->get_row("SELECT
                    cat.ID,
                    child_val as `status`
                FROM
                    tp_categories cat
                LEFT JOIN tp_revisions rev ON `rev`.ID = cat.`status` 
                WHERE
                    cat.ID = '$cat_id'");
                
                if (!$check_cat) {
                    return array(
                        "status" => "failed",
                        "message" => "This category does not exists.",
                    );
                
                }elseif ($check_cat->status != '1') {
                    return array(
                        "status" => "failed",
                        "message" => "This category is currently inactive.",
                    );
                }else{
                     $ctid = " `ctid` = '$cat_id', ";
                }
            }

            $product_prev = "None";
            $files = $request->get_file_params();

            if ( isset($files['img'])) {
				$results = DV_Globals::upload_image( $request,$files ); 
                $product_prev = $results['data']; 
                if (!$results){
                    return array(
                        "status" => "failed",
                        "message" => "An error occured while submitting data to database.");
                }
            }
            if ($product_prev === "None"){
                $data = array('title' => $_POST["title"],
                              'short_info' => $_POST["short_info"],
                              'long_info' => $_POST["long_info"],
                              'sku' => $_POST["sku"],
                              'price' => $_POST["price"],
                              'weight' => $_POST["weight"],
                              'dimension' => $_POST["dimension"],
                );
            }
            else{
                $data = array('title' => $_POST["title"],
                              'preview' => $product_prev,
                              'short_info' => $_POST["short_info"],
                              'long_info' => $_POST["long_info"],
                              'sku' => $_POST["sku"],
                              'price' => $_POST["price"],
                              'weight' => $_POST["weight"],
                              'dimension' => $_POST["dimension"],
                );
            }

            $where = array('id' => $user['pdid']); 

            $update = TP_Globals:: custom_update($product_id, $_POST['wpid'], 'products', $table_product, $table_revs, $data, $where );

            if ($update == false) {
                return  array(
                    "status" => "error",
                    "message" => "An error occured while submitting data to the server."
                );
            }
           
            return array(
                "status" => "success",
                "message" => "Data has been updated successfully."
            );
        }

          // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['title']      = $_POST["title"];
                $cur_user['short_info'] = $_POST["short_info"];
                $cur_user['long_info']  = $_POST["long_info"];
                $cur_user['sku']        = $_POST["sku"];
                $cur_user['price']      = $_POST["price"];
                $cur_user['weight']     = $_POST["weight"];
                $cur_user['dimension']  = $_POST["dimension"];
  
                return  $cur_user;
        }

    }