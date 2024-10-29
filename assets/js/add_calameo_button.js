(function(){
    tinymce.PluginManager.requireLangPack('calameo_button');
    tinymce.create('tinymce.plugins.CalameoButton', {
        init : function(ed) {
            ed.addCommand('mceCalameo', function(){
                ed.windowManager.open({
                    file    : '../wp-content/plugins/athlon-manage-calameo-publications/assets/js/libs/calameo_dialog.php',
                    width   : 400,
                    height  : 400,
                    inline  : 1
                });
            });
            ed.addButton('calameo_button', {
                    title   : 'Calaméo',
                    cmd     : 'mceCalameo',
                    image   : '../wp-content/plugins/athlon-manage-calameo-publications/assets/images/shortcode2.jpg'
            });
        }});
})();
tinymce.PluginManager.add('calameo_button', tinymce.plugins.CalameoButton);