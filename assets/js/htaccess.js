jQuery(document).ready(function($) {
    $('.add-htaccess-rule').on('click', function() {
        var button = $(this);
        var option = button.data('option');
        
        button.prop('disabled', true).text('Adding...');
        
        $.ajax({
            url: nerdDelayHtaccess.ajaxurl,
            type: 'POST',
            data: {
                action: 'nerd_delay_add_htaccess_rule',
                option: option,
                nonce: nerdDelayHtaccess.nonce
            },
            success: function(response) {
                if (response.success) {
                    button.removeClass('button-primary').addClass('button-disabled').text('Already Added');
                    $('<span class="activated-message">Active on lines: ' + response.data.lines.join(', ') + '</span>').insertAfter(button);
                    alert(nerdDelayHtaccess.success);
                } else {
                    button.prop('disabled', false).text('Add to .htaccess');
                    alert(response.data.message || nerdDelayHtaccess.error);
                }
            },
            error: function() {
                button.prop('disabled', false).text('Add to .htaccess');
                alert(nerdDelayHtaccess.error);
            }
        });
    });
    
    // Add a "Remove All" button functionality
    $('#remove-all-htaccess').on('click', function() {
        if (confirm('Are you sure you want to remove all Nerd Delay rules from your .htaccess file? This action cannot be undone.')) {
            var button = $(this);
            button.prop('disabled', true).text('Removing...');
            
            $.ajax({
                url: nerdDelayHtaccess.ajaxurl,
                type: 'POST',
                data: {
                    action: 'nerd_delay_remove_all_htaccess_rules',
                    nonce: nerdDelayHtaccess.nonce
                },
                success: function(response) {
                    button.prop('disabled', false).text('Remove All Rules');
                    if (response.success) {
                        $('.add-htaccess-rule').prop('disabled', false).text('Add to .htaccess')
                            .removeClass('button-disabled').addClass('button-primary');
                        $('.activated-message').remove();
                        alert('All Nerd Delay rules have been removed from your .htaccess file.');
                    } else {
                        alert(response.data.message || 'Error removing rules from .htaccess.');
                    }
                },
                error: function() {
                    button.prop('disabled', false).text('Remove All Rules');
                    alert('Error removing rules from .htaccess.');
                }
            });
        }
    });
});