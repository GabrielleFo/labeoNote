<?php
/*
Plugin Name: Gestion des Frais Mensuels
Description: Plugin pour gérer les frais mensuels avec un tableau quotidien.
Version: 1.0
Author: Votre Nom
*/

ob_start();

// Sécurité : empêcher l'accès direct
if ( !defined('ABSPATH') ) {
    exit;
}

// Inclure les fichiers nécessaires

include_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';
include_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';
include_once plugin_dir_path(__FILE__) . 'includes/capacites-personalisees.php';
include_once plugin_dir_path(__FILE__) . 'includes/data-table.php';
include_once plugin_dir_path(__FILE__) . 'includes/form.php';
include_once plugin_dir_path(__FILE__) . 'includes/utilisateurs.php';
include_once plugin_dir_path(__FILE__) . 'includes/manager.php';
include_once plugin_dir_path(__FILE__) . 'includes/comptabilite.php';
include_once plugin_dir_path(__FILE__) . 'includes/utilisateurs.php';



// creaton de la table lors de l'activation du pluggin
function frais_update_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $charset_collate = $wpdb->get_charset_collate();

    // Vérifier si la table existe
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        // Si la table n'existe pas, la créer
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            date date NOT NULL,
            type varchar(255) NOT NULL,
            montant float NOT NULL,
            description text NOT NULL,
            piece_jointe varchar(255),
            status enum('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
            user_id bigint(20) UNSIGNED NOT NULL,
            n_plus_1_id bigint(20) UNSIGNED,
            date_validation datetime,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY n_plus_1_id (n_plus_1_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    } else {
        // Si la table existe, afficher un message dans l'administration
        add_action('admin_notices', function() use ($table_name) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>La table <strong>' . esc_html($table_name) . '</strong> existe déjà. Le développeur doit vérifier la table et prendre les précautions nécessaires.</p>';
            echo '</div>';
        });
    }
}
register_activation_hook(__FILE__, 'frais_update_table');



$output = ob_get_clean();
if (!empty($output)) {
    error_log('Sortie inattendue du plugin Frais : ' . $output);
}