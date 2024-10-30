<?php

namespace at\cookers\wp;


class Subscription_Checker_Shortcode
{
    function __construct() {
        add_shortcode( 'sub_show_if', array($this, 'show_if') );
    }

    function show_if( $atts, $content = null ) {
        $atts = shortcode_atts( array(
                                 'subscription' => 'active',
                             ), $atts );

        $user_id = get_current_user_id();

        if(is_user_logged_in()) {
            $usermeta_end_date = get_user_meta($user_id, "subscription_end_date", true);
            $end_date = \DateTime::createFromFormat("d.m.Y", $usermeta_end_date);
            $today = new \DateTime("today");

            if($end_date !== false) {
                if ($today <= $end_date) {
                    if ($atts['subscription'] === 'active') {
                        return do_shortcode($content);
                    }
                } else {
                    if ($atts['subscription'] === 'ended' || $atts['subscription'] === 'inactive') {
                        return do_shortcode($content);
                    }
                }
            } else {
                if ($atts['subscription'] === 'never' || $atts['subscription'] === 'inactive') {
                    return do_shortcode($content);
                }
            }
        } else { // no user logged in
            if ($atts['subscription'] === 'never' || $atts['subscription'] === 'inactive') {
                return do_shortcode($content);
            }
        }
    }
}