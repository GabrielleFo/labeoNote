<?php


// Afficher le formulaire
function frais_user_frais_form() {
    if (current_user_can('submit_frais')) {
    ?>
    <h2>Ajouter un frais</h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="submit_frais">
        <?php wp_nonce_field('frais_nonce_action', 'frais_nonce'); ?>
        <table class="form-table">
        <tr valign="top">
        <th scope="row"><label for="analytique">Code Analytique</label></th>
        <td>
            <input type="text" name="analytique" id="analytique" value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'analytique', true)); ?>" required>
        </td>
    </tr>
            <tr valign="top">
                <th scope="row"><label for="date">Date</label></th>
                <td><input type="date" name="date" id="date" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="type">Type de frais</label></th>
                <td><input type="text" name="type" id="type" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="montant">Montant</label></th>
                <td><input type="number" step="0.01" name="montant" id="montant" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="description">Description</label></th>
                <td><textarea name="description" id="description" rows="5" cols="30" required></textarea></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="piece_jointe">Pièce jointe</label></th>
                <td><input type="file" name="piece_jointe" id="piece_jointe"></td>
            </tr>
            <tr valign="top">
    <th scope="row"><label for="manager">Manager (N+1)</label></th>
    <td>
        <select name="manager" id="manager" required>
            <?php
            $managers = get_users(array('role' => 'manager_n1')); // Assurez-vous d'avoir un rôle 'manager'
            foreach ($managers as $manager) {
                echo '<option value="' . esc_attr($manager->ID) . '">' . esc_html($manager->display_name) . '</option>';
            }
            ?>
        </select>
    </td>
</tr>
        </table>
        <?php submit_button('Ajouter Frais'); ?>
    </form>
    <?php
    } else {
        echo '<p>Vous n\'avez pas les permissions nécessaires pour soumettre des frais.</p>';
    }
}

// Traitement du formulaire pour ajouter un frais
function frais_submit_frais_action() {
    if (current_user_can('submit_frais') && isset($_POST['date'], $_POST['type'], $_POST['montant'], $_POST['description'],$_POST['manager'])) {
        // Vérifiez le nonce
        if (!isset($_POST['frais_nonce']) || !wp_verify_nonce($_POST['frais_nonce'], 'frais_nonce_action')) {
            wp_die('Nonce vérification échouée.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';

        $date = sanitize_text_field($_POST['date']);
        $type = sanitize_text_field($_POST['type']);
        $montant = floatval($_POST['montant']);
        $description = sanitize_textarea_field($_POST['description']);
        $user_id = get_current_user_id();

        $piece_jointe = '';
        if (isset($_FILES['piece_jointe']) && !empty($_FILES['piece_jointe']['name'])) {
            $uploaded = media_handle_upload('piece_jointe', 0);
            if (!is_wp_error($uploaded)) {
                $piece_jointe = wp_get_attachment_url($uploaded);
            }
        }

        $manager_id = intval($_POST['manager']);

        $wpdb->insert($table_name, array(
            'date' => $date,
            'type' => $type,
            'montant' => $montant,
            'description' => $description,
            'piece_jointe' => $piece_jointe,
            'user_id' => $user_id,
            'manager_id' => $manager_id,
            'status' => 'en_attente'
        ));

        wp_redirect(admin_url('admin.php?page=gestion-des-frais'));
        exit;
    } else {
        wp_die('Vous n\'avez pas les permissions nécessaires pour soumettre des frais.');
    }
}
add_action('admin_post_submit_frais', 'frais_submit_frais_action');