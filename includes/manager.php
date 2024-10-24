<?php
function frais_display_validation_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'frais';
    $current_user = wp_get_current_user();
    $table_usermeta = $wpdb->prefix . 'usermeta';

    // Récupérer les frais en attente pour le manager N-1 et N+2
    $manager_n1_id = get_user_meta($current_user->ID, 'manager', true);
    $current_user_id = $current_user->ID;

    // Récupérer les frais en attente avec le code analytique
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT f.*, um.meta_value AS analytique
         FROM $table_name f
         LEFT JOIN $table_usermeta um ON f.user_id = um.user_id AND um.meta_key = 'analytique'
         WHERE f.status = 'en_attente' AND (manager_id = %d OR manager_id = %d)",
         $manager_n1_id,
         $current_user_id
    ), OBJECT);

    if ($results) {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php?action=batch_validate_frais')) . '">';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th><input type="checkbox" id="select_all" /><th>Code analytique</th><th>Date</th><th>Type de frais</th><th>Lieu du déplacement</th><th>heure début </th><th>heure fin</th><th>Collaborateur</th><th>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            $user = get_userdata($row->user_id);
            echo '<tr>';
            echo '<td><input type="checkbox" name="frais_ids[]" value="' . esc_attr($row->id) . '" /></td>'; // Case à cocher
            echo '<td>' . esc_html($row->analytique) . '</td>';  // Affichage du code analytique
            echo '<td>' . esc_html($row->date) . '</td>';
            echo '<td>' . esc_html($row->type) . '</td>';
            echo '<td>' . esc_html($row->lieu_deplacement) . '</td>';
            echo '<td>' . esc_html($row->heure_debut) . '</td>';
            echo '<td>' . esc_html($row->heure_fin) . '</td>';
       
            echo '<td>' . esc_html($user->display_name) . '</td>';
          
            echo '<td>
                    <a href="' . esc_url(admin_url('admin-post.php?action=validate_frais&id=' . $row->id)) . '" style="color: green; font-size: 20px;" title="Valider">&#10004;</a>
                    <a href="' . esc_url(admin_url('admin-post.php?action=refuse_frais&id=' . $row->id)) . '" style="color: red; font-size: 15px;" title="Refuser">&#10060;</a>
                   
                    <a href="' . admin_url('admin-post.php?action=download_excel&id=' . esc_attr($row->id)) . '" target="_blank">
                    <i class="fas fa-file-excel fa-2x "></i> 
                    </a>
                   
                  </td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        ?>
        <style>
            .selection-valider,.selection-annuler {
                margin: 10px; 
                color: white;
                padding: 10px 20px; 
                font-size: 16px; 
                border: none; 
                border-radius: 5px; 
                cursor: pointer; 
             }

            .selection-valider {
                background-color: #007cba; 
            }

            .selection-annuler{
                background-color: #dc3232;
            }
            
        </style>
        <?php
        // Boutons pour validation ou refus des notes sélectionnées
        echo '<input type="submit" name="batch_validate" value="Valider sélectionnées" class="selection-valider" />';
        echo '<input type="submit" name="batch_refuse" value="Refuser sélectionnées" class="selection-annuler" />';
       
        echo '</form>';
    } else {
        echo '<p class="attente">Aucun frais en attente de validation.</p>';
    }

      // Script JavaScript pour gérer la sélection ( je n'arrive pas a le mettre dans le fichier a part javascript)
      ?>
      <script type="text/javascript">
      jQuery(document).ready(function($) {
         $('#select_all').click(function() {
              var checkedStatus = this.checked;
              $('input[name="frais_ids[]"]').each(function() {
                 this.checked = checkedStatus;
             });
         });
      });
      </script>
       <?php
}

// Action pour valider individuellement
function validate_frais_action() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';
        $current_user = wp_get_current_user();
        
        // Récupérez les détails du frais
        $frais = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_GET['id']));
        
        // Vérifiez si l'utilisateur actuel est le manager désigné
        if ($frais && $frais->manager_id == $current_user->ID) {
            $wpdb->update(
                $table_name,
                array(
                    'status' => 'valide',
                    'n_plus_1_id' => $current_user->display_name,
                    'date_validation' => current_time('mysql') // Ajouter la date de validation
                ),
                array('id' => $_GET['id']),
                array('%s', '%s', '%s'), // Assurez-vous d'inclure le format de la date
                array('%d')
            );
            wp_redirect(admin_url('admin.php?page=gestion-des-frais&message=validated'));
        } else {
            wp_redirect(admin_url('admin.php?page=gestion-des-frais&error=not_authorized'));
        }
    } else {
        wp_redirect(admin_url('admin.php?page=gestion-des-frais&error=invalid_id'));
    }
    exit;
}
add_action('admin_post_validate_frais', 'validate_frais_action');

function refuse_frais_action() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';
        $current_user = wp_get_current_user();
        $validator_name = $current_user->display_name;
        $wpdb->update(
            $table_name,
            array(
                'status' => 'refuse',
                'n_plus_1_id' => $validator_name
            ),
            array('id' => $_GET['id']),
            array('%s', '%s'),
            array('%d')
        );
    }
    wp_redirect(admin_url('admin.php?page=gestion-des-frais'));
    exit;
}
add_action('admin_post_refuse_frais', 'refuse_frais_action');

// Action pour valider plusieurs frais
function batch_validate_frais_action() {
    if (isset($_POST['frais_ids']) && is_array($_POST['frais_ids'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'frais';
        $current_user = wp_get_current_user();

        foreach ($_POST['frais_ids'] as $frais_id) {
            $frais = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $frais_id));
            if ($frais && $frais->manager_id == $current_user->ID) {
                $wpdb->update(
                    $table_name,
                    array(
                        'status' => 'valide',
                        'n_plus_1_id' => $current_user->display_name
                    ),
                    array('id' => $frais_id),
                    array('%s', '%s'),
                    array('%d')
                );
            }
        }
        wp_redirect(admin_url('admin.php?page=gestion-des-frais&message=validated'));
    } else {
        wp_redirect(admin_url('admin.php?page=gestion-des-frais&error=no_selection'));
    }
    exit;
}
add_action('admin_post_batch_validate_frais', 'batch_validate_frais_action');

