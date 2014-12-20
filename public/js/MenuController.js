MenuController = function (document, jQuery) {
    var self = {
        init: function (jQuery) {
            jQuery('#expandGroups').on("click", this.expandGroups);
            jQuery('#collapseGroups').on("click", this.collapseGroups);
            jQuery('#hideCollapsed').on("click", this.hideCollapsed);
            jQuery('#showCollapsed').on("click", this.showCollapsed);
        },
        expandGroups: function (event) {
            $(".bookmarkgroup").removeClass('hidden');
        },
        collapseGroups: function (event) {
            $(".bookmarkgroup").addClass('hidden');
        },
        hideCollapsed: function (event) {
            $("#groups").addClass("hideCollapsed");
            jQuery('#hideCollapsed').hide();
            jQuery('#showCollapsed').show();
        },
        showCollapsed: function (event) {
            $("#groups").removeClass("hideCollapsed");
            jQuery('#hideCollapsed').show();
            jQuery('#showCollapsed').hide();
        }
    };

    self.init(jQuery);

    return self;
};
