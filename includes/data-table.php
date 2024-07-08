<?php
// Fonction pour afficher le tableau récapitulatif des frais
// function frais_display_data_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $current_user_id = get_current_user_id();

    // Fonction pour afficher le tableau des frais pour les administrateurs et la comptabilité

    
    // Fonction pour afficher le tableau des frais pour les administrateurs et la comptabilité
    function frais_display_data_table_admin() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';
        $results = $wpdb->get_results("SELECT f.*, u.display_name as user_name, n.display_name as n_plus_1_name 
                                       FROM $table_name f 
                                       LEFT JOIN {$wpdb->users} u ON f.user_id = u.ID 
                                       LEFT JOIN {$wpdb->users} n ON f.n_plus_1_id = n.ID 
                                       ORDER BY f.date DESC", ARRAY_A);
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Description</th>
                    <th>Pièce jointe</th>
                    <th>Statut</th>
                    <th>Validé par</th>
                    <th>Date de validation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo esc_html($row['date']); ?></td>
                    <td><?php echo esc_html($row['user_name']); ?></td>
                    <td><?php echo esc_html($row['type']); ?></td>
                    <td><?php echo esc_html($row['montant']); ?> €</td>
                    <td><?php echo esc_html($row['description']); ?></td>
                    <td><?php echo $row['piece_jointe'] ? '<a href="' . esc_url($row['piece_jointe']) . '" target="_blank">Voir</a>' : 'Aucune'; ?></td>
                    <td><?php echo esc_html($row['status']); ?></td>
                    <td><?php echo esc_html($row['n_plus_1_name']); ?></td>
                    <td><?php echo $row['date_validation'] ? esc_html($row['date_validation']) : ''; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }



// Fonction pour afficher le tableau des frais pour les N+1
function frais_display_data_table_n_plus_1() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    // Code pour afficher les frais à valider
   
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT f.*, u.display_name as user_name 
         FROM $table_name f 
         LEFT JOIN {$wpdb->users} u ON f.user_id = u.ID 
         WHERE f.status = 'en_attente' AND f.user_id != %d
         ORDER BY f.date DESC", 
        $current_user_id
    ), ARRAY_A);
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Utilisateur</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Description</th>
                <th>Pièce jointe</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo esc_html($row['date']); ?></td>
                <td><?php echo esc_html($row['user_name']); ?></td>
                <td><?php echo esc_html($row['type']); ?></td>
                <td><?php echo esc_html($row['montant']); ?> €</td>
                <td><?php echo esc_html($row['description']); ?></td>
                <td><?php echo $row['piece_jointe'] ? '<a href="' . esc_url($row['piece_jointe']) . '" target="_blank">Voir</a>' : 'Aucune'; ?></td>
                <td>
                    <button class="button validate-frais" data-id="<?php echo $row['id']; ?>">Valider</button>
                    <button class="button reject-frais" data-id="<?php echo $row['id']; ?>">Refuser</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php
}

// Fonction pour afficher le tableau des frais pour les abonnés
function frais_display_data_table_abonne() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    // Code pour afficher les frais de l'abonné avec le statut de validation
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT f.*, n.display_name as n_plus_1_name 
         FROM $table_name f 
         LEFT JOIN {$wpdb->users} n ON f.n_plus_1_id = n.ID 
         WHERE f.user_id = %d 
         ORDER BY f.date DESC", 
        $current_user_id
    ), ARRAY_A);
    ?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Description</th>
                <th>Pièce jointe</th>
                <th>Statut</th>
                <th>Validé par</th>
                <th>Date de validation</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo esc_html($row['date']); ?></td>
                <td><?php echo esc_html($row['type']); ?></td>
                <td><?php echo esc_html($row['montant']); ?> €</td>
                <td><?php echo esc_html($row['description']); ?></td>
                <td><?php echo $row['piece_jointe'] ? '<a href="' . esc_url($row['piece_jointe']) . '" target="_blank">Voir</a>' : 'Aucune'; ?></td>
                <td><?php echo esc_html($row['status']); ?></td>
                <td><?php echo esc_html($row['n_plus_1_name']); ?></td>
                <td><?php echo $row['date_validation'] ? esc_html($row['date_validation']) : ''; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}
