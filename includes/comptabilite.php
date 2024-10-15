<?php
function frais_display_export_table() {
    global $wpdb;
    $table_frais = $wpdb->prefix . 'frais';

      // Formulaire pour sélectionner une période
      echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
      echo '<input type="hidden" name="action" value="export_frais">';
      echo '<label for="month">Mois:</label>';
      echo '<select name="month" id="month">';
      for ($m = 1; $m <= 12; $m++) {
          echo '<option value="' . $m . '">' . date("F", mktime(0, 0, 0, $m, 1)) . '</option>';
      }
      echo '</select>';
      
      echo '<label for="year">Année:</label>';
      echo '<input type="number" name="year" id="year" value="' . date('Y') . '" required>';
      
      echo '<input type="submit" value="Exporter les Frais" class="button button-primary">';
      echo '</form>';

    $results = $wpdb->get_results("
    SELECT f.*, um.meta_value AS analytique
    FROM $table_frais f
    LEFT JOIN $wpdb->usermeta um ON f.user_id = um.user_id AND um.meta_key = 'analytique'
    WHERE f.status = 'valide'
", OBJECT);

    if ($results) {
        echo'<p class="manager">Notes de frais validé par les managers </p>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Date</th><th>Type de frais</th><th>Montant</th><th>Description</th><th>Utilisateur</th><th>validé par </th><th>Code analytique</tr></thead>';
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
            echo '<td>' . esc_html($row->analytique) . '</td>'; // Affichage du code analytique
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
    $table_frais = $wpdb->prefix . 'frais';

    $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
    $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

    // Filtrer par période
    $start_date = date('Y-m-d', strtotime("first day of {$year}-{$month}"));
    $end_date = date('Y-m-d', strtotime("last day of {$year}-{$month} ")); // Fin du mois

    // Récupérer les résultats en filtrant par date
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT f.*, um.meta_value AS analytique
        FROM $table_frais f
        LEFT JOIN $wpdb->usermeta um ON f.user_id = um.user_id AND um.meta_key = 'analytique'
        WHERE f.status = 'valide' AND f.date_validation BETWEEN %s AND %s
    ", $start_date, $end_date), ARRAY_A);

    if ($results) {
        $filename = 'frais_valides_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv;charset=utf-8');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Ajouter le BOM UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, array('Date', 'Type de frais', 'Montant', 'Description', 'Utilisateur', 'Validé par', 'Code analytique'));

        foreach ($results as $row) {
            $user = get_userdata($row['user_id']);
            fputcsv($output, array(
                $row['date'],
                $row['type'],
                $row['montant'],
                $row['description'],
                $user ? $user->display_name : 'Inconnu',
                $row['n_plus_1_id'],
                $row['analytique']
            ));
        }
        fclose($output);
        exit;
    } else {
        // Optionnel : gérer le cas où il n'y a pas de résultats
        wp_die('Aucun frais trouvé pour cette période.');
    }
}
add_action('admin_post_export_frais', 'frais_export_frais_action');

