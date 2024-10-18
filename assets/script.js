jQuery(document).ready(function($) {
    //modification du statut de la note de frais 
    $('.validate-frais, .reject-frais').click(function() {
        var action = $(this).hasClass('validate-frais') ? 'validate' : 'reject';
        var fraisId = $(this).data('id');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_frais_status',
                frais_id: fraisId,
                status: action,
                nonce: fraisAdminData.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la mise à jour du statut');
                }
            }
        });
    });
  
    
        

});


