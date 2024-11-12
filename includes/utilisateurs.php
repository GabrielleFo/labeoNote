<?php
function frais_display_user_frais_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $user_id = get_current_user_id();
    $ticket_restaurant = get_user_meta($user_id, 'ticket_restaurant', true);

    // Traitement de la mise à jour
    if (isset($_POST['update_frais'])) {



        $frais_id = intval($_POST['frais_id']);
        $date = sanitize_text_field($_POST['date']);
        $analytique = sanitize_text_field($_POST['analytique']);
        $heure_debut = sanitize_text_field($_POST['heure_debut']);
        $heure_fin = sanitize_text_field($_POST['heure_fin']);
        $motif = sanitize_text_field($_POST['type']);
        $montant_repas_midi = sanitize_text_field($_POST['montant_repas_midi']);
        $repas_midi_type = sanitize_text_field($_POST['repas_midi_type']);
        $repas_soir_type = sanitize_text_field($_POST['repas_soir_type']);
        $montant_repas_soir = sanitize_text_field($_POST['montant_repas_soir']);
        $type_nuitee = sanitize_text_field($_POST['type_nuitee']);
        $montant_nuitee= sanitize_text_field($_POST['montant_nuitee']);
        $essence_montant = sanitize_text_field($_POST['essence_montant']);
        $peage_montant = sanitize_text_field($_POST['peage_montant']);
        $taxi_montant = sanitize_text_field($_POST['taxi_montant']);
        $transport_en_commun_montant = sanitize_text_field($_POST['transport_en_commun_montant']);
        $train_montant= sanitize_text_field($_POST['train_montant']);
        $avion_montant= sanitize_text_field($_POST['avion_montant']);
       
        // Mise à jour de la note de frais dans la base de données
        $wpdb->update(
            $table_name,
            array(
            'date' => $date,
            'heure_debut'=> $heure_debut,
            'heure_fin'=> $heure_fin,
            'code_analytique' => $analytique,
            'repas_midi_type'=>$repas_midi_type,
            'montant_repas_midi' => $montant_repas_midi,
            'repas_soir_type'=>$repas_soir_type,
            'montant_repas_soir' => $montant_repas_soir,
            'type_nuitee'=>$type_nuitee,
            'montant_nuitee' => $montant_nuitee,
            'heure_debut' => $heure_debut, 
            'heure_fin' => $heure_fin ,
            'montant_due' => $montant_due,
            'essence_montant' => $essence_montant,
            'peage_montant' => $peage_montant,
            'taxi_montant' => $taxi_montant,
            'transport_en_commun_montant' => $transport_en_commun_montant,
            'train_montant' => $train_montant,
            'avion_montant' => $avion_montant,
            
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
        <p class="tableau">Recapitulatif de vos notes de frais</p>
        <div id="tableau_edition">
        <?php
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Date</th><th>Type de frais</th><th>Lieu du deplacement</th><th>Soumis le </th><th>Statut</th><th>Montant dépensé</th><th>Montant dû</th>
        <th>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->date) . '</td>';
            echo '<td>' . esc_html($row->type) . '</td>';
            echo '<td>' . esc_html($row->lieu_deplacement) . '</td>';
            echo '<td>' . esc_html($row->date_submission) . '</td>';
            echo '<td>' . esc_html($row->status) . '</td>';

            // Calculer le montant dépensé
        $montant_depense = 0;

        // Ajoutez le montant des nuitées
        $montant_depense += (float)$row->montant_nuitee;

        // Ajouter les montants des repas
        $montant_depense += (float)$row->montant_repas_midi;
        $montant_depense += (float)$row->montant_repas_soir;

        // Ajouter les autres frais
        $montant_depense += (float)$row->montant_due;
        $montant_depense += (float)$row->essence_montant;
        $montant_depense += (float)$row->peage_montant;
        $montant_depense += (float)$row->taxi_montant;
        $montant_depense += (float)$row->transport_en_commun_montant;
        $montant_depense += (float)$row->train_montant;
        $montant_depense += (float)$row->avion_montant;

        echo '<td>' . number_format($montant_depense, 2, ',', ' ') . ' €</td>';

        // Calculer le montant dû
        $montant_du = 0;

        // Montant dû pour repas du midi
        if ($row->montant_repas_midi > 0) {
            if ($row->repas_midi_type === 'restaurant') {
                $montant_du += min($row->montant_repas_midi, 15.25);
            } elseif ($row->repas_midi_type === 'magasin') {
                $montant_du += min($row->montant_repas_midi, 9.40);
            }else {
                // Ajouter le montant dépensé si aucun type spécifique
                $montant_du += (float)$row->montant_repas_midi;
            }
        }
        if ($row->montant_repas_soir > 0) {
            if ($row->repas_soir_type === 'restaurant') {
                $montant_du += min($row->montant_repas_soir, 15.25);
            } elseif ($row->repas_soir_type === 'magasin') {
                $montant_du += min($row->montant_repas_soir, 9.40);
           
            }else {
                // Ajouter le montant dépensé si aucun type spécifique
                $montant_du += (float)$row->montant_repas_soir;
            }
        }

        // Déduction si tickets_restaurants est "oui" et qu'un repas est déclaré
        if ($ticket_restaurant === 'yes' && ($row->montant_repas_midi > 0 || $row->montant_repas_soir > 0)) {
            $montant_du -= 4.80;
        }

        // Montant dû pour nuitée
        if ($row->montant_nuitee > 0) {

            if ($row->type_nuitee === 'grande_ville') {
                $montant_du += 120;
            } elseif ($row->type_nuitee === 'province') {
                $montant_du += 88;
            } else {
                // si etranger
                $montant_du += (float)$row->montant_nuitee;
            }
        }

        // Ajouter tous les autres montants déclarés au montant dû
       
        $montant_du += (float)$row->montant_due;
        $montant_du += (float)$row->essence_montant;
        $montant_du += (float)$row->peage_montant;
        $montant_du += (float)$row->taxi_montant;
        $montant_du += (float)$row->transport_en_commun_montant;
        $montant_du += (float)$row->train_montant;
        $montant_du += (float)$row->avion_montant;   

        // Ajouter 45 euros si prime_grand_deplacement est à 1
        if ($row->prime_grand_deplacement == 1) {
            $montant_du += 45;
        }

        // S'assurer que le montant dû ne devient pas négatif
        if ($montant_du < 0) {
            $montant_du = 0;
        }
        

        echo '<td>' . number_format($montant_du, 2, ',', ' ') . ' €</td>';


            // Afficher le lien "Modifier" seulement si la note de frais n'est pas validée
            if ($row->status !== 'valide') {
                ?>
                <style>
                     .custom-icon {
                    font-size: 30px; /* Taille personnalisée */
                    color: green; /* Couleur personnalisée */
                    }
                    .trash{
                        color:red;
                    }
                </style>
                <?php
                // suppression de la note de frais 
                if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
                    $id = intval($_GET['id']);
                    
                    // Vérifiez ici si l'ID est valide et que l'enregistrement n'est pas validé
                    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    
                    if ($row && $row->status !== 'valide') {
                     
                        $wpdb->delete($table_name, array('id' => $id));
                        
                        // Redirigez après la suppression
                        wp_redirect(admin_url('admin.php?page=gestion-des-frais&message=deleted'));
                        exit;
                    } else {
                        // Gestion des erreurs, par exemple rediriger avec un message d'erreur
                        wp_redirect(admin_url('admin.php?page=gestion-des-frais&message=error'));
                        exit;
                    }
                }
                
                echo '<td>
                <a href="' . admin_url('admin.php?page=gestion-des-frais&action=edit&id=' . esc_attr($row->id) . '#formulaire_edition') . '"><i class="fas fa-pencil-alt custom-icon"></i></a>
                <a href="' . admin_url('admin.php?page=gestion-des-frais&action=delete&id=' . esc_attr($row->id)) . '" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette note de frais ?\');">
                <i class="fas fa-trash-alt fa-2x trash"></i></a>
                
                </td>';
            } else {
                echo '<td>
                <a href="' . admin_url('admin-post.php?action=download_excel&id=' . esc_attr($row->id)) . '" target="_blank">
                <i class="fas fa-file-excel fa-2x "></i> 
            </a>
                </td>';
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
     if (isset($_GET['message'])) {
        if ($_GET['message'] === 'deleted') {
            echo '<div class="notice notice-success is-dismissible"><p>La note de frais a été supprimée avec succès.</p></div>';
        } elseif ($_GET['message'] === 'error') {
            echo '<div class="notice notice-error is-dismissible"><p>Erreur lors de la suppression de la note de frais.</p></div>';
        }
    }
     echo $message;

    // Mode édition d'une note de frais (Affiché uniquement en mode édition)
    if ($is_edit_mode) {
        $frais_id = intval($_GET['id']);
        $frais = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND user_id = %d", $frais_id, $user_id), OBJECT);

        // Vérifier si la note de frais n'a pas encore été validée
        if ($frais && $frais->status !== 'valide') {
            ?>

            <style>
                #formulaire_edition {
                    max-width: 600px;
                    margin: 50px auto;
                    padding: 20px;
                    border: 1px solid #ccc;
                    border-radius: 10px;
                    background-color: #f9f9f9;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }
                .form-row {
                    display: flex; /* Utiliser Flexbox pour l'alignement */
                    align-items: center; /* Aligner verticalement les éléments */
                    margin-bottom: 15px; /* Espacement entre les lignes */
                }

                .form-row label {
                    flex: 1; /* Équilibre l'espace entre les labels et les champs */
                    margin-right: 10px; /* Espacement entre le label et le champ */
                }

                .form-row input,
                .form-row select {
                    flex: 2; /* Plus d'espace pour les champs */
                }
                .analytique {
                    margin-bottom: 10px;
                    display: flex; /* Utiliser Flexbox pour le centrage */
                    justify-content: center; /* Centrer horizontalement */
                    align-items: center; /* Centrer verticalement */
                }
                
    
            </style>
            <div id="formulaire_edition">
            <form method="post" action="">
                <h3>Modification de la note de frais</h3>
                <div class="form-row">
                    <label for="date">Date:</label>
                        <input type="date" name="date" value="<?php echo esc_attr($frais->date); ?>" required>
                    <label for="heure_debut">Heure de début</label>
                        <input type="time" name="heure_debut" id="heure_debut" value="<?php echo esc_attr($frais->heure_debut ? $frais->heure_debut : ''); ?>"> 
                    <label for="heure_fin">Heure de fin</label>
                        <input type="time" name="heure_fin" id="heure_fin" value="<?php echo esc_attr($frais->heure_fin ? $frais->heure_fin : ''); ?>">
                </div>
                <div class="analytique">
                    <!-- champ analystique  -->
                    <label for="analytique" class="analytique">Code Analytique </label>
                        <input type="text" name="analytique" id="analytique" value="<?php echo esc_attr(get_user_meta(get_current_user_id(), 'analytique', true)); ?>" required>
                </div>        
                <div class="form-row">
                <!-- Champ pour lemotif en lecture seulement -->
                    <label for="motif">Motif</label>
                    <input type="text" name="motif" value="<?php echo esc_attr($frais->type); ?> "readonly>

                    <label for="motif">Lieu</label>
                    <input type="text" name="lieu" value="<?php echo esc_attr($frais->lieu_deplacement); ?> "readonly>
                </div>
                <div class="form-row">
                    <!-- Récupérer les données depuis la base de données -->
                    <?php $repas_midi_type = $frais->repas_midi_type;  ?>
                    <!-- Champ pour le type de repas midi -->

                    <label for="repas_midi_type">Type de repas (Midi)</label>
                    <select name="repas_midi_type" id="repas_midi_type">
                        <option value="">Sélectionnez un type</option>
                        <option value="restaurant" <?php echo ($repas_midi_type === 'restaurant') ? 'selected' : ''; ?>>Restaurant</option>
                        <option value="magasin" <?php echo ($repas_midi_type === 'magasin') ? 'selected' : ''; ?>>Achats magasins</option>
                    </select>

                    <!-- Champ pour le montant des repas midi -->
                    <label for="montant_repas_midi">Montant repas midi:</label>
                        <input type="number" name="montant_repas_midi" value="<?php echo esc_attr($frais->montant_repas_midi); ?>">
                </div>
                <div class="form-row">
          
                    <!-- Récupérer le type de repas soir depuis la base de données -->
                    <?php $repas_soir_type = $frais->repas_soir_type; ?>

                    <!-- Champ pour le type de repas soir -->
                    <label for="repas_soir_type">Type de repas (Soir)</label>
                    <select name="repas_soir_type" id="repas_soir_type">
                        <option value="">Sélectionnez un type</option>
                        <option value="restaurant" <?php echo ($repas_soir_type === 'restaurant') ? 'selected' : ''; ?>>Restaurant</option>
                        <option value="magasin" <?php echo ($repas_soir_type === 'magasin') ? 'selected' : ''; ?>>Achats magasins</option>
                    </select>

                    <!-- Champ pour le montant des repas soir -->
                    <label for="montant_repas_soir">Montant repas soir:</label>
                        <input type="number" name="montant_repas_soir" value="<?php echo esc_attr($frais->montant_repas_soir); ?>">
                </div>
                <div class="form-row">
                    <!-- Récupérer le type d'hôtel depuis la base de données -->
                    <?php $type_nuitee = $frais->type_nuitee;  ?>

                    <!-- Champ pour le type d'hôtel -->
                    <label for="type_nuitee">Type d'hôtel</label>
                    <select name="type_nuitee" id="type_nuitee">
                        <option value="">Sélectionnez un type</option>
                        <option value="etranger" <?php echo ($type_nuitee === 'etranger') ? 'selected' : ''; ?>>Etranger</option>
                        <option value="province" <?php echo ($type_nuitee === 'province') ? 'selected' : ''; ?>>Province</option>
                        <option value="grande_ville" <?php echo ($type_nuitee === 'grande_ville') ? 'selected' : ''; ?>>Grande ville</option>
                    </select>

                    <!-- Champ pour le montant des nuitées -->
                    <label for="montant_nuitee">Montant nuitée:</label>
                        <input type="number" name="montant_nuitee" value="<?php echo esc_attr($frais->montant_nuitee); ?>">
                </div>
                <div class="form-row">
                    <!-- Autres champs pour les frais -->
                    <label for="essence_montant">Montant essence:</label>
                    <input type="number" name="essence_montant" value="<?php echo esc_attr($frais->essence_montant); ?>">

                    <label for="peage_montant">Montant péage:</label>
                    <input type="number" name="peage_montant" value="<?php echo esc_attr($frais->peage_montant); ?>">
                </div>
                <div class="form-row">
                    <label for="taxi_montant">Montant taxi:</label>
                    <input type="number" name="taxi_montant" value="<?php echo esc_attr($frais->taxi_montant); ?>">

                    <label for="transport_en_commun_montant">Montant transport en commun:</label>
                    <input type="number" name="transport_en_commun_montant" value="<?php echo esc_attr($frais->transport_en_commun_montant); ?>">
                </div>
                <div class="form-row">

                    <label for="train_montant">Montant train:</label>
                    <input type="number" name="train_montant" value="<?php echo esc_attr($frais->train_montant); ?>">

                    <label for="avion_montant">Montant avion:</label>
                    <input type="number" name="avion_montant" value="<?php echo esc_attr($frais->avion_montant); ?>">
                </div>

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
?>

