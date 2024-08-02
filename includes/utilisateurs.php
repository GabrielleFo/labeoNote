<?php


function frais_display_user_frais_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $user_id = get_current_user_id();

    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id), OBJECT);
    
    if ($results) {
        ?>
        <p class="tableau">Vos notes de frais </p>
        <?php
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Date</th><th>Type de frais</th><th>Montant</th><th>Description</th><th>Statut</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->date) . '</td>';
            echo '<td>' . esc_html($row->type) . '</td>';
            echo '<td>' . esc_html($row->montant) . '</td>';
            echo '<td>' . esc_html($row->description) . '</td>';
            echo '<td>' . esc_html($row->status) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Aucun frais trouvé.</p>';
    }
}