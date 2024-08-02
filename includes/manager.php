<?php
function frais_display_validation_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $current_user = wp_get_current_user();

    // Récupérer seulement les frais en attente dont le manager_id correspond à l'utilisateur actuel
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE status = 'en_attente' AND manager_id = %d",
        $current_user->ID
    ), OBJECT);
    
    if ($results) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Date</th><th>Type de frais</th><th>Montant</th><th>Description</th><th>Collaborateur</th><th>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            $user = get_userdata($row->user_id);
            echo '<tr>';
            echo '<td>' . esc_html($row->date) . '</td>';
            echo '<td>' . esc_html($row->type) . '</td>';
            echo '<td>' . esc_html($row->montant) . '</td>';
            echo '<td>' . esc_html($row->description) . '</td>';
            echo '<td>' . esc_html($user->display_name) . '</td>';
            echo '<td>
                    <a href="' . esc_url(admin_url('admin-post.php?action=validate_frais&id=' . $row->id)) . '">Valider</a>
                    <a href="' . esc_url(admin_url('admin-post.php?action=refuse_frais&id=' . $row->id)) . '">Refuser</a>
                  </td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p class="attente">Aucun frais en attente de validation.</p>';
    }
}

// Actions pour valider ou refuser un frais
function validate_frais_action() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';
        $current_user = wp_get_current_user();
        
        // Récupérez les détails du frais
        $frais = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_GET['id']));
        
        // Vérifiez si l'utilisateur actuel est le manager désigné
        if ($frais && $frais->manager_id == $current_user->ID) {
            $wpdb->update(
                $table_name,
                array(
                    'status' => 'valide',
                    'n_plus_1_id' => $current_user->display_name
                ),
                array('id' => $_GET['id']),
                array('%s', '%s'),
                array('%d')
            );
            wp_redirect(admin_url('admin.php?page=gestion-des-frais&message=validated'));
        } else {
            wp_redirect(admin_url('admin.php?page=gestion-des-frais&error=not_authorized'));
        }
    } else {
        wp_redirect(admin_url('admin.php?page=gestion-des-frais&error=invalid_id'));
    }
    exit;
}
add_action('admin_post_validate_frais', 'validate_frais_action');

function refuse_frais_action() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';
        $current_user = wp_get_current_user();
        $validator_name = $current_user->display_name;
        $wpdb->update(
            $table_name,
            array(
                'status' => 'refuse',
                'n_plus_1_id' => $validator_name
            ),
             array('id' => $_GET['id']),
              array('%s','%s'),
               array('%d'));
    }
    wp_redirect(admin_url('admin.php?page=gestion-des-frais'));
    exit;
}
add_action('admin_post_refuse_frais', 'refuse_frais_action');