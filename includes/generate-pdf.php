<?php
add_action('admin_post_download_excel', 'download_excel');

function download_excel() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais'; // Assurez-vous que c'est le bon nom de table

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        // Récupérer uniquement la note de frais correspondant à l'ID fourni
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND status = 'valide'", $id));

        if ($row) {
            // Préparation de l'export
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="note_de_frais_' . $id . '.xls"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Ajouter le BOM pour UTF-8
            echo "\xEF\xBB\xBF"; // Ajout du BOM

            // Titre
            echo "<h1 style='text-align:center;'>Note de Frais</h1>"; // Titre centré
            // Ouvrir la table HTML
            echo "<table border='1'>
                    <tr>";

            // En-têtes
            if (!empty($row->code_analytique)) echo "<th>Code analytique</th>";
            if (!empty($row->lieu_de_deplacement)) echo "<th>Lieu de deplacement</th>";
            if (!empty($row->date)) echo "<th>Date</th>";
            if (!empty($row->type)) echo "<th>Motif</th>";
            if (!empty($row->heure_debut)) echo "<th>Heure début</th>";
            if (!empty($row->heure_fin)) echo "<th>Heure fin</th>";
            if (!empty($row->n_plus_1_id)) echo "<th>Validé par</th>";
            if (!empty($row->date_validation)) echo "<th>Date de validation</th>";
            if (!empty($row->repas_midi_type)) echo "<th>Type repas midi</th>";
            if (!empty($row->montant_repas_midi)) echo "<th>Montant repas midi</th>";
            if (!empty($row->repas_soir_type)) echo "<th>Type repas soir</th>";
            if (!empty($row->montant_repas_soir)) echo "<th>Montant repas soir</th>";
            if (!empty($row->type_nuitee))echo "<th>type nuitee</th>";
            if (!empty($row->montant_nuitee)) echo "<th>montant nuitée</th>";
            if (!empty($row->type_vehicule)) echo "<th>type véhicule</th>";
            if (!empty($row->puissance_fiscale)) echo "<thpuissance véhicule</th>";
            if (!empty($row->kilometres))  echo "<th>kilometres</th>";
            if (!empty($row->montant_due)) echo "<th>montant du</th>";
            if (!empty($row->essence_montant)) echo "<th>montant essence</th>";
            if (!empty($row->peage_montant)) echo "<th>montant péage</th>";
            if (!empty($row->taxi_montant))  echo "<th>montant taxi</th>";
            if (!empty($row->transport_en_commun_montant))  echo "<th>montant transport en commun</th>";
            if (!empty($row->train_montant))  echo "<th>montant train</th>";
           
            if (!empty($row->avion_montant)) echo "<th>montant avion</th>";
         

            echo "</tr>";

            // Remplir les données
            echo "<tr>";
            if (!empty($row->code_analytique)) echo "<td>{$row->code_analytique}</td>";
            if (!empty($row->lieu_de_deplacement)) echo "<td>{$row->lieu_de_deplacement}</td>";
            if (!empty($row->date)) echo "<td>{$row->date}</td>";
            if (!empty($row->type)) echo "<td>{$row->type}</td>";
            if (!empty($row->heure_debut)) echo "<td>{$row->heure_debut}</td>";
            if (!empty($row->heure_fin)) echo "<td>{$row->heure_fin}</td>";
            if (!empty($row->n_plus_1_id)) echo "<td>{$row->n_plus_1_id}</td>";
            if (!empty($row->date_validation)) echo "<td>{$row->date_validation}</td>";
            if (!empty($row->repas_midi_type)) echo "<td>{$row->repas_midi_type}</td>";
            if (!empty($row->montant_repas_midi)) echo "<td>{$row->montant_repas_midi}</td>";
            if (!empty($row->repas_soir_type)) echo "<td>{$row->repas_soir_type}</td>";
            if (!empty($row->montant_repas_soir)) echo "<td>{$row->montant_repas_soir}</td>";
            if (!empty($row->type_nuitee)) echo "<td>{$row->type_nuitee}</td>";
            if (!empty($row->montant_nuitee)) echo "<td>{$row->montant_nuitee}</td>";
            if (!empty($row->type_vehicule))echo "<td>{$row->type_vehicule}</td>";
            if (!empty($row->puissance_fiscale))echo "<td>{$row->puissance_fiscale}</td>";
            if (!empty($row->kilometres)) echo "<td>{$row->kilometres}</td>";
            if (!empty($row->montant_due))  echo "<td>{$row->montant_due}</td>";
            if (!empty($row->essence_montant)) echo "<td>{$row->essence_montant}</td>";
            if (!empty($row->peage_montant)) echo "<td>{$row->peage_montant}</td>";
            if (!empty($row->taxi_montant))  echo "<td>{$row->taxi_montant}</td>";
            if (!empty($row->transport_en_commun_montant))echo "<td>{$row->transport_en_commun_montant}</td>";
            if (!empty($row->train_montant)) echo "<td>{$row->train_montant}</td>";
            if (!empty($row->avion_montant)) echo "<td>{$row->avion_montant}</td>";
            
            echo "</tr>";

            echo "</table>";
            // Afficher les pièces jointes en dehors du tableau
            if (!empty($row->piece_jointe_repas_midi)) {
                echo "<h3>Pièces jointes (repas midi):</h3>";
             
                echo "<div><img src='{$row->piece_jointe_repas_midi}' alt='Image' /></div>";
             
            }

            exit;
        } else {
            wp_die('La note de frais n\'est pas valide ou n\'existe pas.');
        }
    } else {
        wp_die('Aucun ID fourni.');
    }
}
