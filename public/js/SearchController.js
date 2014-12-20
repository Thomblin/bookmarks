SearchController = function (document, jQuery) {
    var self = {
        show: null,
        init: function (jQuery) {
            jQuery('.search input').keyup(this.loadSuggest);
            jQuery('.search input').on("click", this.showSuggest);

            jQuery(document).on("click", this.hideSuggest);
            jQuery(".search").delegate("li", "click", this.handleSuggestClick);

            jQuery('#search').focus();
        },
        loadSuggest: function(event) {

            if ( event && event.which && 13 == event.which) {
                self.showSearchResults();
                return;
            }

            self.searchWords();
        },
        searchWords: function () {
            var data = {
                search: jQuery('.search input').val(),
                action: "search"
            };
            var jqxhr = jQuery
                .post(document.URL, data, function () {
                })
                .done(function (words) {

                    self.show = false;
                    var ul = jQuery('<ul>');

                    jQuery.each(words, function (index, value) {
                        ul.append(jQuery('<li>' + value + '</li>'));
                        self.show = true;
                    });

                    jQuery('.search .suggest').html(ul);
                    self.showSuggest();
                })
                .fail(function (data) {
                    if (data.responseJSON.message) {
                        alert("Could not search: " + data.responseJSON.message);
                    } else {
                        alert("Could not search");
                    }
                });
        },
        showSuggest: function (event) {
            event && self.stopPropagation(event);

            if (self.show) {
                jQuery('.search .suggest').show();
            } else if ( null === self.show && "" != jQuery('.search input').val() ) {
                self.loadSuggest();
            } else {
                jQuery('.search .suggest').hide();
            }
        },
        hideSuggest: function (event) {
            jQuery('.search .suggest').hide();
        },
        stopPropagation: function (event) {
            event.stopPropagation();
            return false;
        },
        handleSuggestClick: function (event) {

            event && jQuery('.search input').val($(this).html());

            self.showSearchResults(event);
        },
        showSearchResults: function (event) {

            var data = {
                search: jQuery('.search input').val(),
                action: "show"
            };
            var jqxhr = jQuery
                .post(document.URL, data, function () {
                    jQuery('.search .suggest').hide();
                    $("p.bookmark").removeClass("highlight");
                    $(".bookmarkgroup").addClass('hidden');
                })
                .done(function (ids) {
                    jQuery.each(ids, function (index, value) {
                        jQuery("#"+value+".bookmarkgroup").removeClass('hidden');
                        jQuery("p#"+value+".bookmark").addClass("highlight");
                    });
                })
                .fail(function (data) {
                    if ( data.responseJSON.message ) {
                        alert("Could not load links: " + data.responseJSON.message);
                    } else {
                        alert("Could not load links");
                    }
                });
        }
    };

    self.init(jQuery);

    return self;
};
