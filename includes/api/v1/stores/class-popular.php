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

    class TP_Popular_Store {
        public static function popular_store(){
            global $wpdb;
               
            // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			//step 2: validate User
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"])  ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
            }
            

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            // Step6 : Sanitize all Request if emply
			if (empty($_POST["wpid"]) || empty($_POST["snky"])  ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empyty.",
					)
                );
                
            }

            $table_product = TP_PRODUCT_TABLE;

            $table_product_revs = TP_PRODUCT_REVS_TABLE;

            $table_stores = TP_STORES_TABLE;

            $table_stores_revs = TP_STORES_REVS_TABLE;
        

            $table_categories = TP_CATEGORIES_TABLE;

            $table_categories_revs = TP_CATEGORIES_REVS_TABLE;

            $tp_revs = TP_REVISION_TABLE;

            // datavice table variables declarations
            $dv_geo_brgy = DV_BRGY_TABLE;
            $dv_revs    =  DV_REVS_TABLE;
            $dv_address = DV_ADDRESS_TABLE;
            $dv_geo_city = DV_CTY_TABLE;
            $dv_geo_prov = DV_PRV_TABLE;
            $dv_geo_court = DV_COUNTRY_TABLE;

            $mp_orders = MP_ORDER_TABLE;

            $result = $wpdb->get_results("SELECT
                Count( mp_ord.stid ) AS cnt,
                mp_ord.stid,
                ( SELECT rev.child_val FROM tp_revisions rev WHERE ID = tp_st.title ) AS `title`,
                ( SELECT rev.child_val FROM tp_revisions rev WHERE ID = tp_st.short_info ) AS `short_info`,
                ( SELECT rev.child_val FROM tp_revisions rev WHERE ID = tp_st.long_info ) AS `long_info`,
                ( SELECT rev.child_val FROM tp_revisions rev WHERE ID = tp_st.banner ) AS `banner`,
                ( SELECT dv_revisions.child_val FROM dv_revisions WHERE ID = dv_add.street ) AS `street`,
                ( SELECT brgy_name FROM dv_geo_brgy WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.brgy ) ) AS brgy,
                ( SELECT citymun_name FROM dv_geo_city WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.city ) ) AS city,
                ( SELECT prov_name FROM dv_geo_province WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.province ) ) AS province,
                ( SELECT country_name FROM dv_geo_countries WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.country ) ) AS country 
            FROM
                $mp_orders mp_ord
                INNER JOIN $table_stores tp_st ON mp_ord.stid = tp_st.ID
                INNER JOIN $tp_revs tp_rev ON tp_st.title = tp_rev.ID
                INNER JOIN $dv_address dv_add ON tp_st.address = dv_add.ID 
            GROUP BY
                mp_ord.stid", OBJECT);



            if(empty($result)){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator.",
                    )
                );
            }else{
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                        
                        )
                    )
                );
            }
        }
    }