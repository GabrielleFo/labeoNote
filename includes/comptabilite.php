<?php
function frais_display_export_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';

    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'valide'", OBJECT);

    if ($results) {
        echo'<p class="manager">Notes de frais validé par les managers </p>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Date</th><th>Type de frais</th><th>Montant</th><th>Description</th><th>Utilisateur</th><th>validé par </th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            $user = get_userdata($row->user_id);
            $validator = get_userdata($row->n_plus_1_id);
            echo '<tr>';
            echo '<td>' . esc_html($row->date) . '</td>';
            echo '<td>' . esc_html($row->type) . '</td>';
            echo '<td>' . esc_html($row->montant) . '</td>';
            echo '<td>' . esc_html($row->description) . '</td>';
            echo '<td>' . esc_html($user->display_name) . '</td>';
            echo '<td>' . esc_html($row->n_plus_1_id) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Aucun frais à exporter.</p>';
    }
}

function frais_export_button() {
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="export_frais">';
    submit_button('Exporter les Frais');
    echo '</form>';
}

function frais_export_frais_action() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';


    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'valide'", ARRAY_A);

    if ($results) {
        $filename = 'frais_valides_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv;charset=utf-8');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

         // Ajouter le BOM UTF-8
         fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

         ?>
        
         <?php
        fputcsv($output, array('Date', 'Type de frais', 'Montant', 'Description','utilisateur','Validé par'));

        foreach ($results as $row) {
            $user = get_userdata($row['user_id']);
            $validator = get_userdata($row['n_plus_1_id']);
            fputcsv($output, array(
                $row['date'], 
                $row['type'],
                 $row['montant'],
                $row['description'], 
                $user->display_name,
                $row['n_plus_1_id']
            ));
        }
        fclose($output);
        exit;
    } 
}
add_action('admin_post_export_frais', 'frais_export_frais_action');