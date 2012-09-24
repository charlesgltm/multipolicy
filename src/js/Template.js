var Template = Class.create({
    
    initMainWindow: function(elementId, windowTitle) {
        jQuery('#' + elementId).dialog({
            title: windowTitle,
            autoOpen: true,
            closeOnEscape: false,
            draggable: false,
            resizable: false,
            modal: true,
            beforeClose: function() {
                return false;
            },
            width: 800,
            height: 600
        });
    },
})