<?php
/*
Plugin Name: Formulaire de soumission de frais
Description: Permet aux utilisateurs de soumettre des frais avec plusieurs champs personnalisés.
*/

global $wpdb;

// Fonction pour récupérer tous les utilisateurs ayant le rôle "manager"
function gets_managers_list() {
 
    return get_users(array('role' => 'manager'));
}

function get_current_user_analytical_code() {

    $user_id = get_current_user_id();
    return get_user_meta($user_id, 'analytique', true);
}

// Fonction pour récupérer tous les codes analytiques existants
function get_all_analytical_codes() {
    global $wpdb; // Assurez-vous que $wpdb est accessible dans cette fonction

    // Récupérer tous les codes analytiques uniques à partir des métadonnées utilisateur
    $codes = $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'analytique'");
    return $codes;
}




// Afficher le formulaire
function frais_user_frais_form() {
    if (current_user_can('submit_frais')) {
        // $current_user = wp_get_current_user();
        // $managers = gets_managers_list();
        $current_analytical_code = get_current_user_analytical_code();
        $all_analytical_codes = get_all_analytical_codes(); // Récupère tous les codes analytiques
        ?>
        <h2>Ajouter un frais</h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="submit_frais">
            <?php wp_nonce_field('frais_nonce_action', 'frais_nonce'); ?>
            <table class="form-table">

                <!-- Champ Code Analytique -->
                <tr valign="top">
                    <th scope="row"><label for="analytical_code"><?php _e('Code Analytique', 'textdomain'); ?></label></th>
                    <td>
                    <select name="analytical_code" id="analytical_code">
            <?php if ($current_code): ?>
                <option value="<?php echo esc_attr($current_code); ?>" selected><?php echo esc_html($current_code); ?> (Actuel)</option>
            <?php endif; ?>
            <?php foreach ($all_codes as $code): ?>
                <option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($code); ?></option>
            <?php endforeach; ?>
        </select>
                    </td>
                </tr>

                <!-- Champ Manager -->
                <tr valign="top">
                    <th scope="row"><label for="manager">Manager (N+1)</label></th>
                    <td>
                        <select name="manager" id="manager" required>
                            <?php foreach ($managers as $manager) : ?>
                                <option value="<?php echo esc_attr($manager->ID); ?>" <?php selected($current_user->manager, $manager->ID); ?>>
                                    <?php echo esc_html($manager->display_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <!-- Champ Date -->
                <tr valign="top">
                    <th scope="row"><label for="date">Date</label></th>
                    <td><input type="date" name="date" id="date" required></td>
                </tr>

                <!-- Champ Heure de Début -->
                <tr valign="top">
                    <th scope="row"><label for="heure_debut">Heure de Début</label></th>
                    <td><input type="time" name="heure_debut" id="heure_debut" required></td>
                </tr>

                <!-- Champ Heure de Fin -->
                <tr valign="top">
                    <th scope="row"><label for="heure_fin">Heure de Fin</label></th>
                    <td><input type="time" name="heure_fin" id="heure_fin" required></td>
                </tr>

                <!-- Champ Motif -->
                <tr valign="top">
                    <th scope="row"><label for="motif">Motif</label></th>
                    <td>
                        <select name="motif" id="motif" required>
                            <option value="mission">Mission</option>
                            <option value="formation">Formation</option>
                            <option value="congres">Congrès</option>
                            <option value="reunion">Réunion</option>
                            <option value="salon_exposition">Salon & Exposition</option>
                            <option value="autre">Autre</option>
                        </select>
                    </td>
                </tr>

                <!-- Champ Lieu de Déplacement -->
                <tr valign="top">
                    <th scope="row"><label for="lieu_deplacement">Lieu de Déplacement</label></th>
                    <td><input type="text" name="lieu_deplacement" id="lieu_deplacement" required></td>
                </tr>

                <!-- Frais de Repas : Midi -->
                <tr valign="top">
                    <th scope="row"><label for="repas_midi_mode">Repas Midi</label></th>
                    <td>
                        <select name="repas_midi_mode" id="repas_midi_mode">
                            <option value="restaurant">Restaurant</option>
                            <option value="emporter">À Emporter</option>
                        </select>
                        <input type="number" step="0.01" name="repas_midi_montant" id="repas_midi_montant" placeholder="Montant (€)">
                        <input type="file" name="repas_midi_piece_jointe" id="repas_midi_piece_jointe">
                    </td>
                </tr>

                <!-- Frais de Repas : Soir -->
                <tr valign="top">
                    <th scope="row"><label for="repas_soir_mode">Repas Soir</label></th>
                    <td>
                        <select name="repas_soir_mode" id="repas_soir_mode">
                            <option value="restaurant">Restaurant</option>
                            <option value="emporter">À Emporter</option>
                        </select>
                        <input type="number" step="0.01" name="repas_soir_montant" id="repas_soir_montant" placeholder="Montant (€)">
                        <input type="file" name="repas_soir_piece_jointe" id="repas_soir_piece_jointe">
                    </td>
                </tr>

                <!-- Frais de Transport -->
                <tr valign="top">
                    <th scope="row"><label for="transport_mode">Frais de Transport</label></th>
                    <td>
                        <select name="transport_mode" id="transport_mode">
                            <option value="aucun">Aucun</option>
                            <option value="vehicule_labeo">Véhicule Labéo</option>
                            <option value="vehicule_personnel">Véhicule Personnel</option>
                        </select>
                        <input type="number" name="puissance_fiscale" id="puissance_fiscale" placeholder="Puissance Fiscale" style="display: none;">
                        <input type="file" name="carte_grise_piece_jointe" id="carte_grise_piece_jointe" style="display: none;">
                        <input type="number" step="0.01" name="km_parcourus" id="km_parcourus" placeholder="KM Parcourus" style="display: none;">
                        <input type="number" step="0.01" name="frais_carburant" id="frais_carburant" placeholder="Frais de Carburant (€)" style="display: none;">
                        <input type="number" step="0.01" name="frais_peages" id="frais_peages" placeholder="Frais de Péages (€)" style="display: none;">
                    </td>
                </tr>

                <!-- Autres Moyens de Transport -->
                <tr valign="top">
                    <th scope="row"><label for="autres_transport">Autres Moyens de Transport</label></th>
                    <td>
                        <input type="text" name="autres_transport" id="autres_transport" placeholder="Train, Taxi, Avion, etc.">
                    </td>
                </tr>

                <!-- Champ Nuitée -->
                <tr valign="top">
                    <th scope="row"><label for="nuit_location">Nuitée</label></th>
                    <td>
                        <select name="nuit_location" id="nuit_location">
                            <option value="province">Province</option>
                            <option value="grande_ville">Grande Ville</option>
                        </select>
                        <input type="number" step="0.01" name="nuit_montant" id="nuit_montant" placeholder="Montant (€)">
                        <input type="file" name="nuit_piece_jointe" id="nuit_piece_jointe">
                    </td>
                </tr>

                <!-- Bouton de soumission -->
                <tr valign="top">
                    <td colspan="2"><?php submit_button('Ajouter Frais'); ?></td>
                </tr>
            </table>
        </form>
        <?php
    } else {
        echo '<p>Vous n\'avez pas les permissions nécessaires pour soumettre des frais.</p>';
    }
}

// Traitement du formulaire pour ajouter un frais
function frais_submit_frais_action() {
    if (current_user_can('submit_frais') && isset($_POST['date'], $_POST['heure_debut'], $_POST['heure_fin'], $_POST['motif'], $_POST['lieu_deplacement'])) {
        // Vérifiez le nonce
        if (!isset($_POST['frais_nonce']) || !wp_verify_nonce($_POST['frais_nonce'], 'frais_nonce_action')) {
            wp_die('Nonce vérification échouée.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';

        $date = sanitize_text_field($_POST['date']);
        $heure_debut = sanitize_text_field($_POST['heure_debut']);
        $heure_fin = sanitize_text_field($_POST['heure_fin']);
        $motif = sanitize_text_field($_POST['motif']);
        $lieu_deplacement = sanitize_text_field($_POST['lieu_deplacement']);
        $user_id = get_current_user_id();
        $manager_id = intval($_POST['manager']);
        $code_analytique = intval($_POST['code_analytique']);

        // Sauvegarde des fichiers joints et des données correspondantes

        $piece_jointe = '';
        if (isset($_FILES['piece_jointe']) && !empty($_FILES['piece_jointe']['name'])) {
            $uploaded = media_handle_upload('piece_jointe', 0);
            if (!is_wp_error($uploaded)) {
                $piece_jointe = wp_get_attachment_url($uploaded);
            }
        }

        // Insertion des données dans la base de données
        $wpdb->insert($table_name, array(
            'date' => $date,
            'heure_debut' => $heure_debut,
            'heure_fin' => $heure_fin,
            'motif' => $motif,
            'lieu_deplacement' => $lieu_deplacement,
            'user_id' => $user_id,
            'manager_id' => $manager_id,
            'code_analytique' => $code_analytique,
            'status' => 'en_attente'
        ));

        wp_redirect(admin_url('admin.php?page=gestion-des-frais'));
        exit;
    } else {
        wp_die('Vous n\'avez pas les permissions nécessaires pour soumettre des frais.');
    }
}
add_action('admin_post_submit_frais', 'frais_submit_frais_action');