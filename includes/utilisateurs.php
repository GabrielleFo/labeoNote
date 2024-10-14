<?php

function frais_display_user_frais_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $user_id = get_current_user_id();

    // Traitement de la demande d'édition
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

        // Rediriger vers la page sans les paramètres `action` et `id`
        wp_redirect(admin_url('admin.php?page=gestion-des-frais&updated=true'));
        exit;
    }

    // Affichage du formulaire d'édition
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $frais_id = intval($_GET['id']);
        $frais = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND user_id = %d", $frais_id, $user_id), OBJECT);

        if ($frais) {
            ?>
            <form method="post" action="">
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
            <?php
        } else {
            echo '<p>Note de frais introuvable.</p>';
        }
        return;
    }

    // Afficher un message de confirmation après mise à jour
    if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
        echo '<div class="updated notice"><p>La note de frais a été mise à jour avec succès.</p></div>';
    }

    // Affichage des notes de frais
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id), OBJECT);

    if ($results) {
        ?>
        <p class="tableau">Vos notes de frais</p>
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
            echo '<td><a href="' . admin_url('admin.php?page=gestion-des-frais&action=edit&id=' . esc_attr($row->id)) . '">Modifier</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Aucun frais trouvé.</p>';
    }
}
