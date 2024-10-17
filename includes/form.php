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
                <th scope="row"><label for="date">Date du déplacement</label></th>
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
                <th scope="row"><label for="description">Lieu de déplacement </label></th>
                <td><input tupe="text" name="description" id="description"  required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="repas_midi_type">Type de repas (Midi)</label></th>
                <td>
                    <select name="repas_midi_type" id="repas_midi_type" required>
                        <option value="">Sélectionnez un type</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="magasin">Achats magasins</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="montant_repas_midi">Montant</label></th>
                <td><input type="number" step="0.01" name="montant_repas_midi" id="montant_repas_midi" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="piece_jointe_repas_midi">Preuve de dépense (Midi)</label></th>
                <td><input type="file" name="piece_jointe_repas_midi" id="piece_jointe_repas_midi"></td>
            </tr>

            <!-- Repas du soir, visible uniquement si la case nuitée est cochée -->
            <tr valign="top" id="repas_soir_row" style="display: none;">
                <th scope="row"><label for="repas_soir_type">Type de repas (Soir)</label></th>
                <td>
                    <select name="repas_soir_type" id="repas_soir_type">
                        <option value="">Sélectionnez un type</option>
                        <option value="restaurant">Restaurant</option>
                        <option value="magasin">Achats magasins</option>
                    </select>
                </td>
            </tr>
            <tr valign="top" id="montant_repas_soir_row" style="display: none;">
                <th scope="row"><label for="montant_repas_soir">Montant</label></th>
                <td><input type="number" step="0.01" name="montant_repas_soir" id="montant_repas_soir"></td>
            </tr>
            <tr valign="top" id="piece_jointe_repas_soir_row" style="display: none;">
                <th scope="row"><label for="piece_jointe_repas_soir">Preuve de dépense (Soir)</label></th>
                <td><input type="file" name="piece_jointe_repas_soir" id="piece_jointe_repas_soir"></td>
            </tr>

            <label for="frais_transport">Avez-vous utilisé un véhicule ? </label>
                <select id="frais_transport" name="frais_transport" onchange="toggleVehicule(this.value)">
                    <option value="non">Non</option>
                    <option value="oui">Oui</option>
                </select>

            <div id="vehicule_section" style="display: none;">
                <label for="vehicule">Type de véhicule</label>
                <select id="vehicule" name="vehicule" onchange="togglePuissance(this.value)">
                    <option value="">--Sélectionner--</option>
                    <option value="societe">Véhicule de société</option>
                    <option value="personnel">Véhicule personnel</option>
                   
                </select>
            </div>
            <div id="puissance_section" style="display: none;">
                <label for="puissance">Puissance fiscale</label>
                <select id="puissance" name="puissance" onchange="calculMontant()">
                    <option value="3">3 CV et moins (0.529 €/km)</option>
                    <option value="4">4 CV (0.606 €/km)</option>
                    <option value="5">5 CV (0.636 €/km)</option>
                    <option value="6">6 CV (0.665 €/km)</option>
                    <option value="7">7 CV et plus (0.697 €/km)</option>
                </select>
            </div>
            <div id="kilometres_section" style="display: none;">
                <label for="kilometres">Nombre de kilomètres parcourus</label>
                <input type="number" id="kilometres" name="kilometres" min="0" onchange="calculMontant()">
            </div>
            <div id="montant_section" style="display: none;">
                <label for="montant">Montant dû</label>
                <input type="text" id="montant" name="montant" readonly>
            </div>
            <div id="societe_fields" style="display: none;">
                <label for="essence_montant">Montant essence/électricité :</label>
                <input type="number" name="essence_montant" id="essence_montant">

                <label for="piece_jointe_essence">Preuve essence/électricité :</label>
                <input type="file" name="piece_jointe_essence" id="piece_jointe_essence">
            </div>

            <!-- Section Péage, visible pour les deux types de véhicules -->
            <div id="peage_fields" style="display: none;">
                <label for="peage_montant">Montant du péage :</label>
                <input type="number" name="peage_montant" id="peage_montant">

                <label for="piece_jointe_peage">Preuve de péage :</label>
                <input type="file" name="piece_jointe_peage" id="piece_jointe_peage">
            </div>
            <label for="autres_frais_transport">Avez-vous d'autres frais de transport ?</label>
            
            <select id="autres_frais_transport" name="autres_frais_transport" onchange="toggleAutresFraisTransport(this.value)">
                <option value="non">Non</option>
                <option value="oui">Oui</option>
            </select>

            <div id="autres_frais_section" style="display: none;">
                <div id="taxi_fields">
                    <label for="taxi_montant">Montant frais de taxi :</label>
                    <input type="number" name="taxi_montant" id="taxi_montant">

                    <label for="piece_jointe_taxi">Preuve frais de taxi :</label>
                    <input type="file" name="piece_jointe_taxi" id="piece_jointe_taxi">
                </div>

                <div id="transport_en_commun_fields">
                    <label for="transport_en_commun_montant">Montant frais de transport en commun :</label>
                    <input type="number" name="transport_en_commun_montant" id="transport_en_commun_montant">

                    <label for="piece_jointe_transport_en_commun">Preuve frais de transport en commun :</label>
                    <input type="file" name="piece_jointe_transport_en_commun" id="piece_jointe_transport_en_commun">
                </div>

                <div id="train_fields">
                    <label for="train_montant">Montant frais de train :</label>
                    <input type="number" name="train_montant" id="train_montant">

                    <label for="piece_jointe_train">Preuve frais de train :</label>
                    <input type="file" name="piece_jointe_train" id="piece_jointe_train">
                </div>

                <div id="avion_fields">
                    <label for="avion_montant">Montant frais d'avion :</label>
                    <input type="number" name="avion_montant" id="avion_montant">

                    <label for="piece_jointe_avion">Preuve frais d'avion :</label>
                    <input type="file" name="piece_jointe_avion" id="piece_jointe_avion">
                </div>
            </div>

            <!-- ------------------------------------------------------------------ -->
            <tr valign="top">
                <th scope="row"><label for="montant">Montant</label></th>
                <td><input type="number" step="0.01" name="montant" id="montant" required></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="piece_jointe">Pièce jointe</label></th>
                <td><input type="file" name="piece_jointe" id="piece_jointe"></td>
            </tr>


            <!-- ----------------------------------------------------------------- -->
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

        function toggleNuiteeFields() {
            var nuitéeChecked = document.getElementById('nuitee').checked;
            var nuitéeFields = document.getElementById('nuitee_fields');
            var montantNuiteeRow = document.getElementById('montant_nuitee_row');
            var primeGrandDeplacementRow = document.getElementById('prime_grand_deplacement_row');
            var pieceJointeNuiteeRow = document.getElementById('piece_jointe_nuitee_row');

            var repasSoirRow = document.getElementById('repas_soir_row');
            var montantRepasSoirRow = document.getElementById('montant_repas_soir_row');
            var pieceJointeRepasSoirRow = document.getElementById('piece_jointe_repas_soir_row');

                // Affichez ou masquez les champs de nuitée
                if (nuitéeChecked) {
                    nuitéeFields.style.display = '';
                    montantNuiteeRow.style.display = '';
                    primeGrandDeplacementRow.style.display = '';
                    pieceJointeNuiteeRow.style.display = '';

                    // Affichez les champs de repas du soir
                    repasSoirRow.style.display = '';
                    montantRepasSoirRow.style.display = '';
                    pieceJointeRepasSoirRow.style.display = '';
                } else {
                    nuitéeFields.style.display = 'none';
                    montantNuiteeRow.style.display = 'none';
                    primeGrandDeplacementRow.style.display = 'none';
                    pieceJointeNuiteeRow.style.display = 'none';

                    // Masquez les champs de repas du soir
                    repasSoirRow.style.display = 'none';
                    montantRepasSoirRow.style.display = 'none';
                    pieceJointeRepasSoirRow.style.display = 'none';
                }
        }

        function toggleVehicule(value) {
            // Afficher ou masquer la section "Type de véhicule"
            if (value === "oui") {
                document.getElementById('vehicule_section').style.display = 'block';
            } else {
                document.getElementById('vehicule_section').style.display = 'none';
                document.getElementById('puissance_section').style.display = 'none';
                document.getElementById('kilometres_section').style.display = 'none';
                document.getElementById('montant_section').style.display = 'none';
                document.getElementById('societe_fields').style.display = 'none';
                document.getElementById('peage_fields').style.display = 'none';
            }
        }

        function togglePuissance(value) {
            // Réinitialisation des sections pour éviter d'afficher les champs incorrects
            document.getElementById('puissance_section').style.display = 'none';
            document.getElementById('kilometres_section').style.display = 'none';
            document.getElementById('montant_section').style.display = 'none';
            document.getElementById('societe_fields').style.display = 'none';
            document.getElementById('peage_fields').style.display = 'none';

            if (value === "personnel") {

                // Afficher les champs spécifiques au véhicule personnel
                document.getElementById('puissance_section').style.display = 'block';
                document.getElementById('kilometres_section').style.display = 'block';
                document.getElementById('peage_fields').style.display = 'block'; // Champ commun péage

            } else if (value === "societe") {

                // Afficher les champs spécifiques au véhicule de société
                document.getElementById('societe_fields').style.display = 'block';
                document.getElementById('peage_fields').style.display = 'block'; // Champ commun péage
            }
        }

        function calculMontant() {
            let puissance = document.getElementById('puissance').value;
            let kilometres = document.getElementById('kilometres').value;
            let tarif = 0;

            switch (puissance) {
                case '3':
                    tarif = 0.529;
                    break;
                case '4':
                    tarif = 0.606;
                    break;
                case '5':
                    tarif = 0.636;
                    break;
                case '6':
                    tarif = 0.665;
                    break;
                case '7':
                    tarif = 0.697;
                    break;
            }

            if (kilometres > 0 && tarif > 0) {
                let montant = kilometres * tarif;
                document.getElementById('montant').value = montant.toFixed(2) + " €";
                document.getElementById('montant_section').style.display = 'block';
            }
        }
        function toggleAutresFraisTransport(value) {
            document.getElementById('autres_frais_section').style.display = value === 'oui' ? 'block' : 'none';
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

        $frais_transport = isset($_POST['frais_transport']) ? sanitize_text_field($_POST['frais_transport']) : 'non';
        $type_vehicule = ($frais_transport === 'oui' && isset($_POST['vehicule'])) ? sanitize_text_field($_POST['vehicule']) : null;
        

        // Initialiser les montants
        $peage_montant = null;
        $piece_jointe_peage = null;
        $essence_montant = null;
        $piece_jointe_essence = null;

        // Récupérer le montant pour l'essence/électricité
        if ($type_vehicule === 'societe') {
            $essence_montant = isset($_POST['essence_montant']) ? floatval($_POST['essence_montant']) : null;
            if (isset($_FILES['piece_jointe_essence']) && !empty($_FILES['piece_jointe_essence']['name'])) {
                $uploaded_essence = media_handle_upload('piece_jointe_essence', 0);
                if (!is_wp_error($uploaded_essence)) {
                    $piece_jointe_essence = wp_get_attachment_url($uploaded_essence);
                }
            }
        }

        // Récupérer le montant du péage
        if ($type_vehicule) {
            $peage_montant = isset($_POST['peage_montant']) ? floatval($_POST['peage_montant']) : null;
            if (isset($_FILES['piece_jointe_peage']) && !empty($_FILES['piece_jointe_peage']['name'])) {
                $uploaded_peage = media_handle_upload('piece_jointe_peage', 0);
                if (!is_wp_error($uploaded_peage)) {
                    $piece_jointe_peage = wp_get_attachment_url($uploaded_peage);
                }
            }
        }
        // Calculer le montant dû en fonction des kilomètres et de la puissance fiscale
       
       $montant_due = null;
       if ($type_vehicule === 'personnel' && isset($_POST['puissance'], $_POST['kilometres'])) {
           $puissance_fiscale = sanitize_text_field($_POST['puissance']);
           $kilometres = intval($_POST['kilometres']);
           $tarifs_puissance = [
               '3' => 0.529,
               '4' => 0.606,
               '5' => 0.636,
               '6' => 0.665,
               '7' => 0.697
           ];
           if ($kilometres > 0 && isset($tarifs_puissance[$puissance_fiscale])) {
               $montant_due = $kilometres * $tarifs_puissance[$puissance_fiscale];
           }
       }
            // Vérifiez si des autres frais de transport sont déclarés
        if (isset($_POST['autres_frais_transport']) && $_POST['autres_frais_transport'] === 'oui') {
            // Récupérer le montant et la pièce jointe pour les frais de taxi
            $taxi_montant = isset($_POST['taxi_montant']) ? floatval($_POST['taxi_montant']) : null;
            $piece_jointe_taxi = '';
            if (isset($_FILES['piece_jointe_taxi']) && !empty($_FILES['piece_jointe_taxi']['name'])) {
                $uploaded_taxi = media_handle_upload('piece_jointe_taxi', 0);
                if (!is_wp_error($uploaded_taxi)) {
                    $piece_jointe_taxi = wp_get_attachment_url($uploaded_taxi);
                }
            }

            // Récupérer le montant et la pièce jointe pour les frais de transport en commun
            $transport_en_commun_montant = isset($_POST['transport_en_commun_montant']) ? floatval($_POST['transport_en_commun_montant']) : null;
            $piece_jointe_transport_en_commun = '';
            if (isset($_FILES['piece_jointe_transport_en_commun']) && !empty($_FILES['piece_jointe_transport_en_commun']['name'])) {
                $uploaded_transport_en_commun = media_handle_upload('piece_jointe_transport_en_commun', 0);
                if (!is_wp_error($uploaded_transport_en_commun)) {
                    $piece_jointe_transport_en_commun = wp_get_attachment_url($uploaded_transport_en_commun);
                }
            }

            // Récupérer le montant et la pièce jointe pour les frais de train
            $train_montant = isset($_POST['train_montant']) ? floatval($_POST['train_montant']) : null;
            $piece_jointe_train = '';
            if (isset($_FILES['piece_jointe_train']) && !empty($_FILES['piece_jointe_train']['name'])) {
                $uploaded_train = media_handle_upload('piece_jointe_train', 0);
                if (!is_wp_error($uploaded_train)) {
                    $piece_jointe_train = wp_get_attachment_url($uploaded_train);
                }
            }

            // Récupérer le montant et la pièce jointe pour les frais d'avion
            $avion_montant = isset($_POST['avion_montant']) ? floatval($_POST['avion_montant']) : null;
            $piece_jointe_avion = '';
            if (isset($_FILES['piece_jointe_avion']) && !empty($_FILES['piece_jointe_avion']['name'])) {
                $uploaded_avion = media_handle_upload('piece_jointe_avion', 0);
                if (!is_wp_error($uploaded_avion)) {
                    $piece_jointe_avion = wp_get_attachment_url($uploaded_avion);
                }
            }
        }

        $type_nuitee = $nuitee ? sanitize_text_field($_POST['type_nuitee']) : null;
        $montant_nuitee = $nuitee ? floatval($_POST['montant_nuitee']) : null;
        $prime_grand_deplacement = isset($_POST['prime_grand_deplacement']) ? 1 : 0;

        $repas_midi_type = sanitize_text_field($_POST['repas_midi_type']);
        $montant_repas_midi = floatval($_POST['montant_repas_midi']);
        $piece_jointe_repas_midi = '';
        if (isset($_FILES['piece_jointe_repas_midi']) && !empty($_FILES['piece_jointe_repas_midi']['name'])) {
            $uploaded_repas_midi = media_handle_upload('piece_jointe_repas_midi', 0);
            if (!is_wp_error($uploaded_repas_midi)) {
                $piece_jointe_repas_midi = wp_get_attachment_url($uploaded_repas_midi);
            }
        }

        // Pour le repas du soir, si la nuitée est cochée
        $repas_soir_type = null;
        $montant_repas_soir = null;
        $piece_jointe_repas_soir = null;

        if ($nuitee) {
            $repas_soir_type = sanitize_text_field($_POST['repas_soir_type']);
            $montant_repas_soir = floatval($_POST['montant_repas_soir']);
            if (isset($_FILES['piece_jointe_repas_soir']) && !empty($_FILES['piece_jointe_repas_soir']['name'])) {
                $uploaded_repas_soir = media_handle_upload('piece_jointe_repas_soir', 0);
                if (!is_wp_error($uploaded_repas_soir)) {
                    $piece_jointe_repas_soir = wp_get_attachment_url($uploaded_repas_soir);
                }
            }
        }


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

        $date_submission = current_time('mysql');

    // Insertion dans la base de données
        $wpdb->insert($table_name, array(
            'date' => $date,

            'nuitee' => $nuitee,
            'type_nuitee' => $type_nuitee,
            'montant_nuitee' => $montant_nuitee,
            'prime_grand_deplacement' => $prime_grand_deplacement,
            'piece_jointe_nuitee' => $piece_jointe_nuitee,

            'type' => $motif,

            'repas_midi_type' => $repas_midi_type,
            'montant_repas_midi' => $montant_repas_midi,
            'piece_jointe_repas_midi' => $piece_jointe_repas_midi,
            'repas_soir_type' => $repas_soir_type,
            'montant_repas_soir' => $montant_repas_soir,
            'piece_jointe_repas_soir' => $piece_jointe_repas_soir,

            'montant' => $montant,
            'description' => $description,
            'piece_jointe' => $piece_jointe,
            'user_id' => $user_id,
            'manager_id' => $manager,
            'status' => 'en_attente',
            'heure_debut' => $heure_debut, 
            'heure_fin' => $heure_fin ,

            'date_submission' => $date_submission,

            'frais_transport' => $frais_transport,
            'type_vehicule' => $type_vehicule,
            'puissance_fiscale' => $puissance_fiscale,
            'kilometres' => $kilometres,
            'montant_due' => $montant_due,
     
            'essence_montant' => $essence_montant,
            'piece_jointe_essence' => $piece_jointe_essence,

           
            'peage_montant' => $peage_montant,
            'piece_jointe_peage' => $piece_jointe_peage,
            'taxi_montant' => $taxi_montant,
            'piece_jointe_taxi' => $piece_jointe_taxi,
            'transport_en_commun_montant' => $transport_en_commun_montant,
            'piece_jointe_transport_en_commun' => $piece_jointe_transport_en_commun,
            'train_montant' => $train_montant,
            'piece_jointe_train' => $piece_jointe_train,
            'avion_montant' => $avion_montant,
            'piece_jointe_avion' => $piece_jointe_avion
        ));

        wp_redirect(admin_url('admin.php?page=gestion-des-frais'));
        exit;
    } else {
        wp_die('Vous n\'avez pas les permissions nécessaires pour soumettre des frais.');
    }
}
add_action('admin_post_submit_frais', 'frais_submit_frais_action');