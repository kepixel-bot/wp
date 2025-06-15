jQuery(document).ready(function ($) {
    // Define a global function to track custom events
    window.kepixelTrackCustomEvent = function(eventName, eventCategory, eventData) {
        _paq = _paq || [];

        // Create params object
        var params = {
            event_name: eventName || 'custom_event',
            category: eventCategory || 'custom'
        };

        // Add any additional event data
        if (eventData && typeof eventData === 'object') {
            for (var key in eventData) {
                if (eventData.hasOwnProperty(key)) {
                    params[key] = eventData[key];
                }
            }
        }

        // Enhanced ecommerce tracking - trackCustomEvent
        _paq.push(['trackCustomEvent',
            params,                     // params
            {},                         // user_data - already set in main tracking code if user is logged in
            {}                          // custom_data
        ]);

        return true; // For convenience in event handlers
    };

    // Add data-track-event attribute support
    // This allows adding tracking to elements with a simple attribute
    // Example: <button data-track-event="click_button" data-track-category="engagement">Click Me</button>
    $(document).on('click', '[data-track-event]', function() {
        var $el = $(this);
        var eventName = $el.attr('data-track-event');
        var eventCategory = $el.attr('data-track-category') || 'interaction';
        var eventData = {};

        // Get any data-track-* attributes as event data
        $.each(this.attributes, function() {
            if (this.name.indexOf('data-track-') === 0 && this.name !== 'data-track-event' && this.name !== 'data-track-category') {
                var key = this.name.replace('data-track-', '');
                eventData[key] = this.value;
            }
        });

        // Track the event
        kepixelTrackCustomEvent(eventName, eventCategory, eventData);
    });
});
