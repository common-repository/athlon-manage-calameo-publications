jQuery(function() {
   var CalameoInsertDialog = {
        form        : jQuery('#calameodialog'),
        wrapper     : jQuery('#calameo_wrapper'),
        textarea    : jQuery('textarea', this.form),
        buttons : {
            insert: jQuery('input:submit', this.form),
            cancel: jQuery('input:reset', this.form)
        },
        selects : {
            file    : jQuery('#file', this.form),
            mode    : jQuery('#mode', this.form),
            view    : jQuery('#view', this.form),
            size    : jQuery('#size', this.form),
            clickto : jQuery('#clickto', this.form),
            target  : jQuery('#target', this.form),
            clicktarget  : jQuery('#clicktarget', this.form)
        },
        options : {
            size            : jQuery('#size', this.form),
            width           : jQuery('#width', this.form),
            height          : jQuery('#height', this.form),
            destination     : jQuery('#destination', this.form),
            showsharemenu   : jQuery('#showsharemenu', this.form),
            sharemenu       : jQuery('.sharemenu', this.form),
            autoflip        : jQuery('#autoflip', this.form),
            turnauto        : jQuery('.autoflip', this.form),
            page            : jQuery('#page', this.form),
            hidelinks       : jQuery('#hidelinks', this.form)
        },

        init : function() {
            CalameoInsertDialog.selects.file.on('change', function() {
                if(CalameoInsertDialog.selects.file.val()) {
                    CalameoInsertDialog.buttons.insert.removeAttr('disabled').removeClass('ath-disabled');
                } else {
                    CalameoInsertDialog.buttons.insert.attr('disabled','disabled').addClass('ath-disabled');
                }
            });
            jQuery('input, select', CalameoInsertDialog.wrapper).each(function(key, element) {
                jQuery(element).on('change', function() {
                    if( (jQuery(this).is('select') && jQuery(this).val()) || (jQuery(this).is('input') && jQuery(this).val() != undefined) ) {
                        CalameoInsertDialog.textarea.val(CalameoInsertDialog.shortcode);
                    }
               });
            });
            CalameoInsertDialog.form.on('submit', function(e) {
                e.preventDefault();
           });

           /* Handle the enter button press */
           jQuery(window).keypress(function(e) {
                if(e.which == 13 && CalameoInsertDialog.textarea.val() ) {
                    CalameoInsertDialog.insert();
                }
           });

           CalameoInsertDialog.buttons.insert.on('click', function(e) {
                e.preventDefault();
                CalameoInsertDialog.insert();
            });

            CalameoInsertDialog.buttons.cancel.on('click', function(e) {
                e.preventDefault();
                CalameoInsertDialog.cancel();
            });
        },

        insert: function() {
            // insert the contents from the input into the document
            tinyMCEPopup.editor.execCommand('mceInsertContent', false, CalameoInsertDialog.textarea.val());
            CalameoInsertDialog.cancel();
        },

        cancel: function() {
            jQuery(window).unbind('keypress');
            tinyMCEPopup.close();
        },

        shortcode: function() {
            var viewer_size = CalameoInsertDialog.options.size;
            if(viewer_size != 'custom') {
                var viewer_width    = CalameoInsertDialog.options.width.val()  ? CalameoInsertDialog.options.width.val()  : '300';
                var viewer_height   = CalameoInsertDialog.options.height.val() ? CalameoInsertDialog.options.height.val() : '194';
            }
            else {
                viewer_size.val();
            }

            if(CalameoInsertDialog.selects.mode.val() == 'mini') {
                CalameoInsertDialog.options.destination.removeClass('ath-hidden');
                CalameoInsertDialog.options.sharemenu.removeClass(' ath-hidden');
                CalameoInsertDialog.options.turnauto.removeClass(' ath-hidden');
                CalameoInsertDialog.selects.target.removeClass(' ath-hidden');
            } else if(CalameoInsertDialog.selects.mode.val() == 'viewer') {
                CalameoInsertDialog.options.destination.addClass('ath-hidden');
                CalameoInsertDialog.options.sharemenu.addClass(' ath-hidden');
                CalameoInsertDialog.options.turnauto.addClass(' ath-hidden');
                CalameoInsertDialog.selects.target.addClass(' ath-hidden');
            } else {
                CalameoInsertDialog.options.destination.addClass('ath-hidden');
                CalameoInsertDialog.options.turnauto.addClass(' ath-hidden');
                CalameoInsertDialog.selects.target.addClass(' ath-hidden');
                CalameoInsertDialog.options.sharemenu.removeClass(' ath-hidden');
            }
            var mode            = CalameoInsertDialog.selects.mode.val() != 'default' ? ' mode=' + CalameoInsertDialog.selects.mode.val() : '';
            var view            = CalameoInsertDialog.selects.view.val() != 'default' ? ' view=' + CalameoInsertDialog.selects.view.val() : '';
            var page            = CalameoInsertDialog.options.page.val() ? ' page=' + jQuery('#page').val() : '';
            var clickto         = (CalameoInsertDialog.options.destination.is(':visible')) ? ' clickto=' + CalameoInsertDialog.selects.clickto.val() : '';
            var hidelinks       = CalameoInsertDialog.options.hidelinks.is(':checked') ? '' : ' hidelinks=1';
            var autoflip        = CalameoInsertDialog.options.autoflip.is(':checked') && CalameoInsertDialog.options.turnauto.is(':visible') ? ' autoflip=' + CalameoInsertDialog.options.autoflip.val() : '';
            var showsharemenu   = CalameoInsertDialog.options.showsharemenu.is(':checked') || !CalameoInsertDialog.options.sharemenu.is(':visible') ? '' : ' showsharemenu=false';
            var clicktarget     = (CalameoInsertDialog.options.destination.is(':visible')) ? ' clicktarget=' + CalameoInsertDialog.selects.clicktarget.val() : '';

            var viewer_shortcode = '[calameo'
                            + ' code=' + CalameoInsertDialog.selects.file.val()
                            + ' width=' + viewer_width
                            + ' height=' + viewer_height
                            + view
                            + mode
                            + showsharemenu
                            + page
                            + clickto
                            + hidelinks
                            + autoflip
                            + clicktarget
                            + ']';

            return viewer_shortcode;
        }
   }
   tinyMCEPopup.onInit.add(CalameoInsertDialog.init);
});

function ath_validate_number_input(e) {
    var key = e.charCode;
    var regex = /[0-9]|\./;
    if(key) {
        key = String.fromCharCode(key);

        if(!regex.test(key)) {
            e.preventDefault();

            return false;
        }
    }
};