<?php
// Enqueuer les scripts et styles nécessaires
function frais_enqueue_admin_scripts() {
   
    wp_enqueue_style('frais-style', plugin_dir_url(__FILE__) . '../assets/style.css', array(), '1.0');
    wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/9eaf4b6630.js', array(), null, true);
    wp_enqueue_script('frais-script', plugin_dir_url(__FILE__) . '../assets/script.js', array('jquery'), '1.0', true);

    wp_localize_script('frais-script', 'fraisAdminData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('frais_admin_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'frais_enqueue_admin_scripts');

