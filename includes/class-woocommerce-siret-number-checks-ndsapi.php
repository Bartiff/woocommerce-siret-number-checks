<?php

/**
 * Title
 *
 * Description
 *
 * @since      0.1.0
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/includes
 * @author     Bartiff <bartiff@gmail.com>
 */
class Woocommerce_Siret_Number_Checks_Ndsapi {

    private static $url_ndsapi = 'https://www.numero-de-siret.com/api/'; 

    private static function do_curl($siret, $newkeys = false) {
        $ch = curl_init();
        if (!$siret) {
            curl_setopt($ch, CURLOPT_URL, SELF::$url_ndsapi . 'siret?siret=');
        } else {
            curl_setopt($ch, CURLOPT_URL, SELF::$url_ndsapi . 'siret?siret=' . $siret);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!$newkeys) {
            curl_setopt($ch, CURLOPT_USERPWD, get_option( 'wsnc-ndsapi-apikey' ) . ':' . get_option( 'wsnc-ndsapi-secretkey' ));
        } else {
            curl_setopt($ch, CURLOPT_USERPWD, $newkeys['api_key'] . ':' . $newkeys['api_secret']);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    public static function test_connection($siret, $apikey, $secretkey) {
        $response = json_decode(SELF::do_curl($siret, [
            'api_key' => $apikey,
            'api_secret' => $secretkey
        ]), true);

        return $response;
    }

    public static function check_siret($siret) {
        $response = json_decode(SELF::do_curl($siret), true);

        return $response;
    }

    public static function verif_siret($name, $siret) {
        $response = SELF::check_siret($siret);
        if ($response['success']) {
            $infos_siret = $response['array_return'];
            if (!empty($infos_siret)) {
                if ($name === $infos_siret[0]['L1_NORMALISEE']) {
                    if ($siret === $infos_siret[0]['SIRET']) {
                        
                        return true;
                    }
                }
            }
        }

        return false;
    }

}