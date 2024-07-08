<?php
function frais_admin_page_content() {
    ?>
    <div class="wrap">
        <h1>Gestion des Frais Mensuels</h1>

        <?php
        // Afficher le formulaire d'ajout de frais pour tous les utilisateurs
        frais_user_frais_form();

        // Obtenir le rôle de l'utilisateur courant
        $user_role = wp_get_current_user()->roles[0];
        var_dump($user_role);

        // Afficher les fonctionnalités supplémentaires en fonction du rôle de l'utilisateur
        if ($user_role == 'administrator' || $user_role == 'comptabilite') {
            frais_display_export_table(); // Tableau pour la comptabilité
            frais_export_button(); // Bouton d'exportation
        }
        
        if ($user_role == 'administrator' || $user_role == 'manager_n1') {
            frais_display_validation_table(); // Tableau de validation pour les N-1
        }

        frais_display_user_frais_table(); // Tableau pour les utilisateurs
        ?>
    </div>
    <?php
}