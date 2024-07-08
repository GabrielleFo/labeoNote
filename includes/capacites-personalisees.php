<?php

// Ajouter la capacité submit_frais aux rôles appropriés
function frais_ensure_custom_capabilities() {
    $roles = array('administrator', 'manager_n1', 'comptabilite', 'colloborateur');
    
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            if (!$role->has_cap('submit_frais')) {
                $role->add_cap('submit_frais');
                error_log('La capacité submit_frais a été ajoutée au rôle : ' . $role_name);
            }
        } else {
            error_log('Le rôle ' . $role_name . ' n\'existe pas.');
        }
    }
}
add_action('init', 'frais_ensure_custom_capabilities');


// Vérifiez les rôles et les capacités des utilisateurs
function frais_check_capabilities() {
    $user = wp_get_current_user();
    error_log('Current user roles: ' . implode(', ', $user->roles));
    error_log('Current user capabilities: ' . json_encode($user->allcaps));

    // Vérifier si la capacité submit_frais est présente
    if (!current_user_can('submit_frais')) {
        error_log('La capacité submit_frais n\'est pas présente pour l\'utilisateur actuel.');
    } else {
        error_log('La capacité submit_frais est présente pour l\'utilisateur actuel.');
    }
}
add_action('init', 'frais_check_capabilities');

// droits des différents roles

function frais_add_custom_roles() {
    // Ajouter le rôle "n_plus_1" avec les capacités personnalisées
    add_role('n_plus_1', 'N+1', array(
        'read' => true,
        'validate_frais' => true,
    ));
    
    // Ajouter le rôle "comptabilite" avec les capacités personnalisées
    add_role('comptabilite', 'Comptabilité', array(
        'read' => true,
        'export_frais' => true,
    ));
}
register_activation_hook(__FILE__, 'frais_add_custom_roles');



// ----------------------------pour les utilisateurs

function frais_add_admin_menu() {
    add_menu_page(
        'Gestion des Frais',
        'Gestion des Frais',
        'read',
        'gestion-des-frais',
        'frais_admin_page_content',
        'dashicons-money',
        6
    );
}
add_action('admin_menu', 'frais_add_admin_menu');



//-------------------------------------- pour les n+1

function frais_add_n_plus_1_menu() {
    
        add_menu_page(
            'Validation des Frais',
            'Validation des Frais',
            'validate_frais',
            'validation-frais',
            'frais_n_plus_1_page',
            'dashicons-yes',
            7
        );
    
}
add_action('admin_menu', 'frais_add_n_plus_1_menu');

function frais_n_plus_1_page() {
    ?>
    <div class="wrap">
        <h1>Validation des Frais</h1>
        <?php frais_display_validation_table(); ?>
    </div>
    <?php
}

// ----------------------------pour la comptaabilité

function frais_add_comptabilite_menu() {
   
        add_menu_page(
            'Exportation des Frais',
            'Exportation des Frais',
            'export_frais',
            'export-frais',
            'frais_comptabilite_page',
            'dashicons-download',
            8
        );
    
}
add_action('admin_menu', 'frais_add_comptabilite_menu');

function frais_comptabilite_page() {
    ?>
    <div class="wrap">
        <h1>Exportation des Frais</h1>
        <?php frais_display_export_table(); ?>
        <?php frais_export_button(); ?>
    </div>
    <?php
}

