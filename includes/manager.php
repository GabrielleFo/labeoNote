<?php
function frais_display_validation_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';

    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'en_attente'", OBJECT);
    
    if ($results) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Date</th><th>Type de frais</th><th>Montant</th><th>Description</th><th>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->date) . '</td>';
            echo '<td>' . esc_html($row->type) . '</td>';
            echo '<td>' . esc_html($row->montant) . '</td>';
            echo '<td>' . esc_html($row->description) . '</td>';
            echo '<td>
                    <a href="' . esc_url(admin_url('admin-post.php?action=validate_frais&id=' . $row->id)) . '">Valider</a>
                    <a href="' . esc_url(admin_url('admin-post.php?action=refuse_frais&id=' . $row->id)) . '">Refuser</a>
                  </td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Aucun frais en attente de validation.</p>';
    }
}

// Actions pour valider ou refuser un frais
function validate_frais_action() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';
        $wpdb->update($table_name, array('status' => 'valide'), array('id' => $_GET['id']), array('%s'), array('%d'));
    }
    wp_redirect(admin_url('admin.php?page=validation-frais'));
    exit;
}
add_action('admin_post_validate_frais', 'validate_frais_action');

function refuse_frais_action() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';
        $wpdb->update($table_name, array('status' => 'refuse'), array('id' => $_GET['id']), array('%s'), array('%d'));
    }
    wp_redirect(admin_url('admin.php?page=validation-frais'));
    exit;
}
add_action('admin_post_refuse_frais', 'refuse_frais_action');