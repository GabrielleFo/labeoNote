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

        

        frais_display_user_frais_table(); // Tableau pour les utilisateurs
        ?>
    </div>
    <?php
}