<?php
function frais_display_user_frais_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $user_id = get_current_user_id();

    // Traitement de la mise à jour
    if (isset($_POST['update_frais'])) {
        $frais_id = intval($_POST['frais_id']);
        $date = sanitize_text_field($_POST['date']);
        $type = sanitize_text_field($_POST['type']);
        $montant = floatval($_POST['montant']);
        $description = sanitize_textarea_field($_POST['description']);

        // Mise à jour de la note de frais dans la base de données
        $wpdb->update(
            $table_name,
            array(
                'date' => $date,
                'type' => $type,
                'montant' => $montant,
                'description' => $description
            ),
            array('id' => $frais_id, 'user_id' => $user_id)
        );

        // Redirection après mise à jour
        wp_redirect(admin_url('admin.php?page=gestion-des-frais&updated=true#tableau_edition'));
        exit;
    }

    // Si une mise à jour a eu lieu, afficher un message de confirmation
    $message = '';
    if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
        $message = '<div class="updated notice"><p>La note de frais a été mise à jour avec succès.</p></div>';
    }

    // Vérification si on est en mode édition
    $is_edit_mode = isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']);

   

    // Affichage du tableau des frais
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id), OBJECT);

    if ($results) {
        ?>
        <p class="tableau">Vos notes de frais</p>
        <div id="tableau_edition">
        <?php
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Date</th><th>Type de frais</th><th>Montant</th><th>Description</th><th>Statut</th><th>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->date) . '</td>';
            echo '<td>' . esc_html($row->type) . '</td>';
            echo '<td>' . esc_html($row->montant) . '</td>';
            echo '<td>' . esc_html($row->description) . '</td>';
            echo '<td>' . esc_html($row->status) . '</td>';

            // Afficher le lien "Modifier" seulement si la note de frais n'est pas validée
            if ($row->status !== 'valide') {
                echo '<td><a href="' . admin_url('admin.php?page=gestion-des-frais&action=edit&id=' . esc_attr($row->id) . '#formulaire_edition') . '">Modifier</a></td>';
            } else {
                echo '<td>-</td>'; // Pas d'action possible si déjà validée
            }
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<p>Aucun frais trouvé.</p>';
    }
     // Affichage du message de confirmation
     echo $message;

    // Mode édition d'une note de frais (Affiché uniquement en mode édition)
    if ($is_edit_mode) {
        $frais_id = intval($_GET['id']);
        $frais = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND user_id = %d", $frais_id, $user_id), OBJECT);

        // Vérifier si la note de frais n'a pas encore été validée
        if ($frais && $frais->status !== 'valide') {
            ?>
            <div id="formulaire_edition">
            <form method="post" action="">
                <h3>Modification de la note de frais</h3>
                <label for="date">Date:</label>
                <input type="date" name="date" value="<?php echo esc_attr($frais->date); ?>" required>

                <label for="type">Type de frais:</label>
                <input type="text" name="type" value="<?php echo esc_attr($frais->type); ?>" required>

                <label for="montant">Montant:</label>
                <input type="number" name="montant" value="<?php echo esc_attr($frais->montant); ?>" required>

                <label for="description">Description:</label>
                <textarea name="description" required><?php echo esc_textarea($frais->description); ?></textarea>

                <input type="hidden" name="frais_id" value="<?php echo esc_attr($frais->id); ?>">
                <input type="submit" name="update_frais" value="Mettre à jour">
            </form>
        </div>
            <?php
        } else {
            echo '<p>Impossible de modifier cette note de frais : elle est déjà validée.</p>';
        }
    }
}
