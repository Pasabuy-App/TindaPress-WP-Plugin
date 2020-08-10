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

    class TP_Newest {
        
        public static function initialize(){
            
            global $wpdb;
            
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

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


             // Step5 : Check if all variables is not empty
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }

            // variable for time stamp
            $later = TP_Globals::date_stamp();

            // variable for newest date
            $date = date_create($later);
            date_sub( $date, date_interval_create_from_date_string("7 days"));
            $date_expected =  date_format($date,"Y-m-d");
            
            // tindapress table variables declarations
            $store_table           = TP_STORES_TABLE;
  
            $categories_table      = TP_CATEGORIES_TABLE;
          
            $product_table         = TP_PRODUCT_TABLE;
            $product_revs_table    = TP_PRODUCT_REVS_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            
            // datavice table variables declarations
            $dv_geo_brgy = DV_BRGY_TABLE;
            $dv_revs    =  DV_REVS_TABLE;
            $dv_address = DV_ADDRESS_TABLE;
            $dv_geo_city = DV_CTY_TABLE;
            $dv_geo_prov = DV_PRV_TABLE;
            $dv_geo_court = DV_COUNTRY_TABLE;

            // step6 : Query
            $result = $wpdb->get_results("SELECT
                st.id,
                cat.types,
                ( SELECT child_val FROM $table_revs WHERE ID = cat.title ) AS cat_title,
                ( SELECT child_val FROM $table_revs WHERE ID = cat.info ) AS cat_inf,
                max( IF ( st_r.child_key = 'title', st_r.child_val, '' ) ) AS title,
                max( IF ( st_r.child_key = 'short_info', st_r.child_val, '' ) ) AS short_info,
                max( IF ( st_r.child_key = 'long_info', st_r.child_val, '' ) ) AS long_info,
                max( IF ( st_r.child_key = 'logo', st_r.child_val, '' ) ) AS logo,o
                max( IF ( st_r.child_key = 'banner', st_r.child_val, '' ) ) AS banner,
                addr.types AS add_types,
                ( SELECT child_val FROM $dv_revs WHERE ID = addr.STATUS ) AS STATUS,
                ( SELECT child_val FROM $dv_address WHERE ID = addr.street ) AS street,
                ( SELECT brgy_name FROM $dv_geo_brgy WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = addr.brgy ) ) AS brgy,
                ( SELECT citymun_name FROM $dv_geo_city WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = addr.city ) ) AS city,
                ( SELECT prov_name FROM $dv_geo_prov WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = addr.province ) ) AS province,
                ( SELECT country_name FROM $dv_geo_court WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = addr.country ) ) AS country 
            FROM
                $store_table st
                INNER JOIN $table_revs st_r ON st.title = st_r.ID 
                OR st.short_info = st_r.ID 
                OR st.long_info = st_r.ID 
                OR st.logo = st_r.ID 
                OR st.banner = st_r.ID
                INNER JOIN $categories_table cat ON st.ctid = cat.ID
                INNER JOIN $dv_address addr ON st.address = addr.ID 
            WHERE  SUBSTRING(st.date_created, 1, 10) BETWEEN '$date_expected' AND '$later'
                GROUP BY
                    st.id 
                ORDER BY
                    RAND( ) 
                LIMIT 10
            ");
    
            // step7 : return result
            return rest_ensure_response( 
                array(
                    "status" => "success",
                    "data" => $result
                    )
                )
            );

        }

    }
