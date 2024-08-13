<?php

// Ajouter la capacité submit_frais aux rôles appropriés
function frais_ensure_custom_capabilities() {
    // Rôles ayant la capacité de soumettre des frais
    $roles_submit = array('administrator', 'manager_n1', 'manager_n2', 'collaborateur', 'maintenance', 'abonnee');
    
    // Rôles ayant la capacité de valider des frais
    $roles_validate = array('administrator', 'manager_n1', 'manager_n2');
    
    // Rôles ayant la capacité d'exporter des frais
    $roles_export = array('administrator', 'comptabilite');
    

}
add_action('init', 'frais_ensure_custom_capabilities');

function frais_revoke_incorrect_capabilities() {
    // Liste des rôles qui ne devraient PAS avoir validate_frais
    $roles_to_revoke = array('colloborateur', 'comptabilite');

    foreach ($roles_to_revoke as $role_name) {
        $role = get_role($role_name);
        if ($role && $role->has_cap('validate_frais')) {
            $role->remove_cap('validate_frais');
            error_log('La capacité validate_frais a été révoquée pour le rôle : ' . $role_name);
        }
    }
}
add_action('init', 'frais_revoke_incorrect_capabilities');


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
    if (!current_user_can('validate_frais')) {
        error_log('La capacité validate_frais n\'est pas présente pour l\'utilisateur actuel.');
    } else {
        error_log('La capacité validate_frais est présente pour l\'utilisateur actuel.');
    }
}
add_action('init', 'frais_check_capabilities');

// droits des différents roles

function frais_add_custom_roles() {
    // Ajouter le rôle "n_plus_1" avec les capacités personnalisées
    add_role('manager_n1', 'N+1', array(
        'read' => true,
        'validate_frais' => true,
    ));
    add_role('manager_n2', 'N+2', array(
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

// 
// validation pour manager n-2

function frais_add_n_plus_2_menu() {
    if (current_user_can('validate_frais')) {
        add_menu_page(
            'Validation des Frais ',
            'Validation des Frais ',
            'validate_frais',
            'validation-frais-n2',
            'frais_n_plus_2_page',
            'dashicons-yes',
            7
        );
    }
}

add_action('admin_menu', 'frais_add_n_plus_2_menu');

function frais_n_plus_2_page() {
    if (!current_user_can('validate_frais')) {
        echo '<p>Vous n\'avez pas les permissions nécessaires pour accéder à cette page.</p>';
        return;
    }
   ?>
    <div class="wrap">
        <h1>Validation des Frais </h1>
        <?php frais_display_validation_table(); ?>
    </div>
    <?php
}

// ----------------------------pour la comptaabilité

function frais_add_comptabilite_menu() {
    if (current_user_can('export_frais')) {
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

