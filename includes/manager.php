<?php
function frais_display_validation_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $current_user = wp_get_current_user();
    $table_frais = $wpdb->prefix . 'frais';  // Nom de votre table des frais
    $table_usermeta = $wpdb->prefix . 'usermeta';  // Nom de votre table usermeta

    // Récupérer les frais en attente pour le manager N-1 et N+2
    $manager_n1_id = get_user_meta($current_user->ID, 'manager', true); // Manager N-1
    $current_user_id = $current_user->ID; // Manager N+2

  // Récupérer les frais en attente avec le code analytique
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT f.*, um.meta_value AS analytique
     FROM $table_frais f
     LEFT JOIN $table_usermeta um ON f.user_id = um.user_id AND um.meta_key = 'analytique'
     WHERE f.status = 'en_attente' AND (manager_id = %d OR manager_id = %d)",
      $manager_n1_id,
      $current_user_id
  ), OBJECT);
    
    if ($results) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Date</th><th>Type de frais</th><th>Montant</th><th>Description</th><th>Collaborateur</th><th>code analytique</th><th>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            $user = get_userdata($row->user_id);
            echo '<tr>';
            echo '<td>' . esc_html($row->date) . '</td>';
            echo '<td>' . esc_html($row->type) . '</td>';
            echo '<td>' . esc_html($row->montant) . '</td>';
            echo '<td>' . esc_html($row->description) . '</td>';
            echo '<td>' . esc_html($user->display_name) . '</td>';
            echo '<td>' . esc_html($row->analytique) . '</td>';  // Affichage du code analytique
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