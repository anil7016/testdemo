if (jQuery().validate) {
    $.validator.addMethod("websitevalidator", function(value, element) {
        return this.optional(element) || /([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/.test(value);
    });
    jQuery.validator.setDefaults({
        ignore: [],
        highlight: function(element) {
            jQuery(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            jQuery(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        validClass: 'has-success',
        errorPlacement: function(error, element) {
            var $element = $(element);
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else if ($element.is(".chzn-select")) {
                error.insertAfter($element.next(".chzn-container"))
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            website: {
                websitevalidator: true
            }
        }, messages: {
            website: {
                websitevalidator: "Please enter valid website",
            }
        }
    });
}