BookmarkController = function (document, jQuery) {
    var self = {
        init: function (jQuery) {
            jQuery('body').on("click", '.add.link', this.showNewLink);
            jQuery('#createNewGroup').on("click", this.showNewGroup);

            jQuery('#new_group button.create').on("click", this.createGroup);
            jQuery('#new_group button.cancel').on("click", this.hideNewGroup);

            jQuery('#new_link button.create').on("click", this.createLink);

            jQuery('#new_link button.cancel').on("click", this.hideNewLink);

            jQuery('body').on("click", '.edit.link', this.addNewTag);
            jQuery('body').on("keypress", '.new_tag', this.completeTag);

            jQuery('body').on("click", 'legend', this.toggleFieldset);
        },
        showNewLink: function (event) {

            // remember which icon was clicked to insert new link before that element
            jQuery("#link_group").val($(this).parents('fieldset.bookmarkgroup').attr('id'));

            jQuery('#link_title').val("");
            jQuery('#link_url').val("");

            self.showElement("#new_link", event);
            jQuery('#blackening').show();
        },
        showNewGroup: function (event) {

            jQuery('#group_name').val("");

            // todo: how to focus create button?
            jQuery('#new_group').find('.create').focus();

            self.showElement("#new_group", event);
            jQuery('#blackening').show();
        },
        hideAll: function () {
            jQuery('#new_group').hide();
            jQuery('#new_link').hide();
        },
        showElement: function (selector, event) {
            self.hideAll();
            jQuery(selector).show();
            jQuery(selector).find('input').first().focus();
        },
        createGroup: function () {
            var data = {
                title: jQuery('#group_name').val(),
                action: "create group"
            };
            var jqxhr = jQuery.post(document.URL, data, function () {
            })
                .done(function (html) {
                    $("#groups").append(html);
                    self.hideNewGroup();
                })
                .fail(function (data) {
                    if ( data.responseJSON.message ) {
                        alert("Could not create new group: " + data.responseJSON.message);
                    } else {
                        alert("Could not create new group");
                    }
                });
        },
        hideNewGroup: function () {
            jQuery('#new_group').hide();
            jQuery('#blackening').hide();
        },
        createLink: function () {
            var data = {
                title: jQuery('#link_title').val(),
                url: jQuery('#link_url').val(),
                group: jQuery("#link_group").val(),
                action: "create link"
            };
            var jqxhr = jQuery.post(document.URL, data, function () {
            })
                .done(function (html) {
                    $(html).insertBefore($('#'+jQuery("#link_group").val() + ' p.new'));
                    self.hideNewLink();
                })
                .fail(function (data) {
                    if ( data.responseJSON.message ) {
                        alert("Could not create new link: " + data.responseJSON.message);
                    } else {
                        alert("Could not create new link");
                    }
                });
        },
        hideNewLink: function () {
            jQuery('#new_link').hide();
            jQuery('#blackening').hide();
        },
        toggleFieldset : function (event) {
            jQuery(this).parent('fieldset').toggleClass('hidden');
        },
        addNewTag: function (event) {
            jQuery(
                "<span>" +
                "<input type=\"text\" placeholder=\"Enter tag. Press Enter to save\" class=\"new_tag\"/>" +
                "</span>"
            ).insertBefore(jQuery(this)).find("input").focus();
        },
        completeTag: function(event) {
            if ( event && event.which && 13 == event.which) {

                var data = {
                    text: jQuery(this).val(),
                    group: jQuery(this).parents('fieldset.bookmarkgroup').attr('id'),
                    link: jQuery(this).parents('p.bookmark').attr('id'),
                    action: "create tag"
                };

                jQuery(this).prop('disabled', true);
                self.createTag(data, this);
            }
        },
        createTag: function (data, target) {
            var jqxhr = jQuery.post(document.URL, data, function () {
            })
                .done(function (html) {
                    $(target).replaceWith(html);
                })
                .fail(function (data) {
                    if ( data.responseJSON.message ) {
                        alert("Could not create new tag: " + data.responseJSON.message);
                    } else {
                        alert("Could not create new tag");
                    }
                });
        }
    };

    self.init(jQuery);

    return self;
};
