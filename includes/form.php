<?php

// Afficher le formulaire
function frais_user_frais_form() {
    if (is_user_logged_in()) {
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
            <?php
            // Récupérer l'ID du manager
            $manager_id = get_user_meta(get_current_user_id(), 'manager', true);

            // Récupérer le display_name du manager
            $manager_name = '';
            if ($manager_id) {
                $manager_data = get_userdata($manager_id);
                if ($manager_data) {
                    $manager_name = $manager_data->display_name;
                }
            }
            ?>
            <tr valign="top">
            <th scope="row"><label for="manager">Manager</label></th>
            <td>
                <input type="text" name="manager" id="manager" value="<?php echo esc_attr($manager_name); ?>" readonly>
            </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="date">Date</label></th>
                <td><input type="date" name="date" id="date" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="heure_debut">Heure de début</label></th>
                <td><input type="time" name="heure_debut" id="heure_debut" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="heure_fin">Heure de fin</label></th>
                <td><input type="time" name="heure_fin" id="heure_fin" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="nuitee">Nuitée</label></th>
                <td>
                    <input type="checkbox" name="nuitee" id="nuitee" onclick="toggleNuiteeFields()">
                </td>
            </tr>
            <tr valign="top" id="nuitee_fields" style="display: none;">
                <th scope="row"><label for="type_nuitee">Type de nuitée</label></th>
                <td>
                    <select name="type_nuitee" id="type_nuitee">
                        <option value="">Sélectionnez un type</option>
                        <option value="etranger">À l'étranger</option>
                        <option value="province">Province</option>
                        <option value="grande_ville">Grande ville</option>
                    </select>
                </td>
            </tr>
            <tr valign="top" id="montant_nuitee_row" style="display: none;">
                <th scope="row"><label for="montant_nuitee">Montant</label></th>
                <td><input type="number" step="0.01" name="montant_nuitee" id="montant_nuitee"></td>
            </tr>
            <tr valign="top" id="prime_grand_deplacement_row" style="display: none;">
                <th scope="row"><label for="prime_grand_deplacement">Prime grand déplacement</label></th>
                <td>
                    <input type="checkbox" name="prime_grand_deplacement" id="prime_grand_deplacement">
                </td>
            </tr>
            <tr valign="top" id="piece_jointe_nuitee_row" style="display: none;">
                <th scope="row"><label for="piece_jointe_nuitee">Preuve de facturation</label></th>
                <td><input type="file" name="piece_jointe_nuitee" id="piece_jointe_nuitee"></td>
            </tr>

            <script type="text/javascript">
                function toggleNuiteeFields() {
                    var nuitéeChecked = document.getElementById('nuitee').checked;
                    var nuitéeFields = document.getElementById('nuitee_fields');
                    var montantNuiteeRow = document.getElementById('montant_nuitee_row');
                    var primeGrandDeplacementRow = document.getElementById('prime_grand_deplacement_row');
                    var pieceJointeNuiteeRow = document.getElementById('piece_jointe_nuitee_row');
                    
                    if (nuitéeChecked) {
                        nuitéeFields.style.display = '';
                        montantNuiteeRow.style.display = '';
                        primeGrandDeplacementRow.style.display = '';
                        pieceJointeNuiteeRow.style.display = '';
                    } else {
                        nuitéeFields.style.display = 'none';
                        montantNuiteeRow.style.display = 'none';
                        primeGrandDeplacementRow.style.display = 'none';
                        pieceJointeNuiteeRow.style.display = 'none';
                    }
                }
            </script>

            <tr valign="top">
                <th scope="row"><label for="motif">Motif</label></th>
                <td>
                    <select name="motif" id="motif" required onchange="toggleAutreMotif()">
                        <option value="">Sélectionnez un motif</option>
                        <option value="mission">Mission</option>
                        <option value="formation">Formation</option>
                        <option value="congres">Congrès</option>
                        <option value="reunion">Réunion</option>
                        <option value="salon">Salon & Exposition</option>
                        <option value="autre">Autre</option>
                    </select>
                </td>
            </tr>
            <tr valign="top" id="motif_autre_row" style="display: none;">
                <th scope="row"><label for="motif_autre">Détail du motif</label></th>
                <td><input type="text" name="motif_autre" id="motif_autre"></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="montant">Montant</label></th>
                <td><input type="number" step="0.01" name="montant" id="montant" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="description">Lieu de déplacement </label></th>
                <td><input tupe="text" name="description" id="description"  required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="piece_jointe">Pièce jointe</label></th>
                <td><input type="file" name="piece_jointe" id="piece_jointe"></td>
            </tr>
            <tr valign="top">
            <th scope="row"><label for="manager_n2">Manager (N+2)</label></th>
                <td>
                <select name="manager_n2" id="manager_n2">
            <option value="">Aucun</option>
            <?php
            $managers_n2 = get_users(array('role' => 'manager_n2'));
            foreach ($managers_n2 as $manager_n2) {
                echo '<option value="' . esc_attr($manager_n2->ID) . '">' . esc_html($manager_n2->display_name) . '</option>';
            }
            ?>
        </select>
                </td>
            </tr>
        </table>
        <?php submit_button('Ajouter Frais'); ?>
    </form>
    <script type="text/javascript">
        function toggleAutreMotif() {
            var motif = document.getElementById('motif').value;
            var motifAutreRow = document.getElementById('motif_autre_row');
            if (motif === 'autre') {
                motifAutreRow.style.display = '';
            } else {
                motifAutreRow.style.display = 'none';
            }
        }
    </script>
    <?php
    } else {
        echo '<p>Vous n\'avez pas les permissions nécessaires pour soumettre des frais.</p>';
    }
}

// Traitement du formulaire pour ajouter un frais
function frais_submit_frais_action() {
    if (is_user_logged_in() && isset($_POST['date'], $_POST['motif'], $_POST['montant'], $_POST['description'],$_POST['manager'],$_POST['heure_debut'],$_POST['heure_fin'])) {
        // Vérifiez le nonce
        if (!isset($_POST['frais_nonce']) || !wp_verify_nonce($_POST['frais_nonce'], 'frais_nonce_action')) {
            wp_die('Nonce vérification échouée.');
        }

        // Vérifiez si la case nuitée est cochée
    $nuitee = isset($_POST['nuitee']) ? 1 : 0;

    // Si la nuitée est cochée, vérifiez les autres champs
    if ($nuitee) {
        if (!isset($_POST['type_nuitee'], $_POST['montant_nuitee'])) {
            wp_die('Champs de nuitée manquants.');
        }
    }


        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';

        $date = sanitize_text_field($_POST['date']);
        $heure_debut = sanitize_text_field($_POST['heure_debut']);
        $heure_fin = sanitize_text_field($_POST['heure_fin']);
        
        $type_nuitee = $nuitee ? sanitize_text_field($_POST['type_nuitee']) : null;
        $montant_nuitee = $nuitee ? floatval($_POST['montant_nuitee']) : null;
        $prime_grand_deplacement = isset($_POST['prime_grand_deplacement']) ? 1 : 0;

        $motif = sanitize_text_field($_POST['motif']);
        $montant = floatval($_POST['montant']);
        $description = sanitize_textarea_field($_POST['description']);
        $user_id = get_current_user_id();
        $manager_n1 = get_user_meta($user_id, 'manager', true); // Manager N-1

        $manager_n2 = isset($_POST['manager_n2']) ? intval($_POST['manager_n2']) : null; // Manager N+2 sélectionné
        $manager = $manager_n2 ? $manager_n2 : $manager_n1; // Choisir le manager N+2 si défini, sinon N-1

        $piece_jointe = '';
        if (isset($_FILES['piece_jointe']) && !empty($_FILES['piece_jointe']['name'])) {
            $uploaded = media_handle_upload('piece_jointe', 0);
            if (!is_wp_error($uploaded)) {
                $piece_jointe = wp_get_attachment_url($uploaded);
            }
        }

        $piece_jointe_nuitee = '';
        if (isset($_FILES['piece_jointe_nuitee']) && !empty($_FILES['piece_jointe_nuitee']['name'])) {
            $uploaded_nuitee = media_handle_upload('piece_jointe_nuitee', 0);
            if (!is_wp_error($uploaded_nuitee)) {
                $piece_jointe_nuitee = wp_get_attachment_url($uploaded_nuitee);
            }
        }

        $manager_id = intval($_POST['manager']);

         // Si le motif est "autre", concaténer avec le détail du motif
         if ($motif === 'autre' && !empty($_POST['motif_autre'])) {
            $motif = 'Autre - ' . sanitize_text_field($_POST['motif_autre']);
        }
    // Insertion dans la base de données
        $wpdb->insert($table_name, array(
            'date' => $date,
            'nuitee' => $nuitee,
            'type_nuitee' => $type_nuitee,
            'montant_nuitee' => $montant_nuitee,
            'prime_grand_deplacement' => $prime_grand_deplacement,
            'piece_jointe_nuitee' => $piece_jointe_nuitee,
            'type' => $motif,
            'montant' => $montant,
            'description' => $description,
            'piece_jointe' => $piece_jointe,
            'user_id' => $user_id,
            'manager_id' => $manager,
            'status' => 'en_attente',
            'heure_debut' => $heure_debut, 
            'heure_fin' => $heure_fin 
        ));

        wp_redirect(admin_url('admin.php?page=gestion-des-frais'));
        exit;
    } else {
        wp_die('Vous n\'avez pas les permissions nécessaires pour soumettre des frais.');
    }
}
add_action('admin_post_submit_frais', 'frais_submit_frais_action');