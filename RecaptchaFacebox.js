
$.fn.RecaptchaFacebox = function(options) {
    
    $(this).click(function() {
        
        // create DOM elements for Facebox and Recaptcha
        if($('#RecaptchaFacebox').html() == null) {
            $('body').prepend('<div id="RecaptchaFacebox" style="display: none;"><p class="message">Please enter the text displayed in the box below, then submit.<br />This helps prevent spam.</p><p class="error">Your answer was incorrect. Please try again.</p><div id="recaptcha"></div><input class="button" type="button" value="submit" /></div>');
        }
        
        $('#RecaptchaFacebox .error').hide();
        
        Recaptcha.create(
            "YOUR_PUBLIC_KEY",
            "recaptcha", {
               theme: "clean",
               callback: Recaptcha.focus_response_field
            }
        );
        
        // call facebox
        jQuery.facebox($('#RecaptchaFacebox'));
        $('#RecaptchaFacebox').show();
    
        $('#RecaptchaFacebox .button').click(function() {
        
            challengeField = $("input#recaptcha_challenge_field").val();
            responseField = $("input#recaptcha_response_field").val();
            
            var valid = $.ajax({
                url: options.url + "?recaptcha_challenge_field=" + challengeField + "&recaptcha_response_field=" + responseField,
                success: function(result) {
                    if(result.toLowerCase() == "true")
                    {
                        $.facebox.close(); 
                        options.successCallback();
                    }
                    else
                    {
                        $('#RecaptchaFacebox .error').show();
                        Recaptcha.reload();
                    }
                }
            });
            
        });
    
    });
    
};
