<?php

add_action('admin_post_download_excel', 'download_excel');

function download_excel() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais'; // Assurez-vous que c'est le bon nom de table

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

        if ($row && $row->status === 'valide') {
            // Préparation de l'export
            $filename = 'note_de_frais_' . $id . '.csv';
            header('Content-Type: text/csv;charset=utf-8');
            header('Content-Disposition: attachment;filename=' . $filename);
            header('Pragma: no-cache');
            header('Expires: 0');

            // Ouvrir la sortie
            $output = fopen('php://output', 'w');

            // Écrire les en-têtes de colonne
            fputcsv($output, array('code analytique', 
            'lieu de deplacement',
            'date',
            'heure debut',
            'heure fin', 
            'valide par ',
            'date de validation',
            'type repas midi ',
            'montant repas midi ',
            'type repas soir',
            'montant repas soir'
            ));

            // Écrire les données de la note de frais
            fputcsv($output, array(
                $row->code_analytique,
                $row->lieu_deplacement,
                $row->date,
                 $row->heure_debut,
                 $row->heure_fin,
                 $row->n_plus_1_id,
                 $row->repas_midi_type,
                 $row->montant_repas_midi,
                 $row->repas_soir_type,
                 $row->montant_repas_soir
                 ));

            fclose($output);
            exit;
        } else {
            wp_die('La note de frais n\'est pas valide ou n\'existe pas.');
        }
    } else {
        wp_die('Aucun ID fourni.');
    }
}
