jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Track form submissions that are likely contact forms
    $(document).on('submit', 'form', function() {
        var $form = $(this);

        // Try to determine if this is a contact form
        // Check for common contact form classes, IDs, or elements
        var isContactForm = false;

        // Check form ID or class
        if (
            $form.attr('id') && (
                $form.attr('id').indexOf('contact') !== -1 ||
                $form.attr('id').indexOf('enquiry') !== -1 ||
                $form.attr('id').indexOf('inquiry') !== -1
            )
        ) {
            isContactForm = true;
        }

        // Check form class
        if (
            $form.attr('class') && (
                $form.attr('class').indexOf('contact') !== -1 ||
                $form.attr('class').indexOf('wpcf7') !== -1 || // Contact Form 7
                $form.attr('class').indexOf('gform') !== -1 || // Gravity Forms
                $form.attr('class').indexOf('ninja') !== -1    // Ninja Forms
            )
        ) {
            isContactForm = true;
        }

        // Check for contact form elements
        if (
            $form.find('input[name*="contact"], input[name*="name"], input[name*="email"], textarea[name*="message"]').length > 2
        ) {
            isContactForm = true;
        }

        // If this appears to be a contact form, track the contact event
        if (isContactForm) {
            // Enhanced ecommerce tracking - trackContact
            _paq.push(['trackContact',
                {},                         // user_data - already set in main tracking code if user is logged in
                {}                          // custom_data
            ]);
        }
    });

    // Special handling for Contact Form 7 (which uses AJAX)
    $(document).on('wpcf7mailsent', function() {
        // Enhanced ecommerce tracking - trackContact
        _paq.push(['trackContact',
            {},                         // user_data - already set in main tracking code if user is logged in
            {}                          // custom_data
        ]);
    });

    // Special handling for Gravity Forms (which can use AJAX)
    $(document).on('gform_confirmation_loaded', function() {
        // Enhanced ecommerce tracking - trackContact
        _paq.push(['trackContact',
            {},                         // user_data - already set in main tracking code if user is logged in
            {}                          // custom_data
        ]);
    });
});
