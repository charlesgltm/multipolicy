
var Functions = Class.create ({
    
    ajaxPath: './src/ajax',
    
    ajaxRequest: function() {
        var ajaxObj = null;
        try {
            ajaxObj = new XMLHttpRequest();
        }
        catch (e) {
            try {
                ajaxObj = new ActiveXObject('Microsoft.XMLHTTP');
            }
            catch (e) {
                ajaxObj = new ActiveXObject('MSXML2.XMLHTTP');
            }
        }
        if (ajaxObj == null) {
            return alert('Your browser may not support this action.\n' +
                         'Please inform this message to your Administrator.');
        }
        else {
            return ajaxObj;
        }
    },
    
    redirect: function (url) {
        window.location.href = url;
    },
    
    textToUpper: function(field) {
        field.value = field.value.toUpperCase();
    },
    
    /**
     * number formatting function
     * copyright Stephen Chapman 24th March 2006, 22nd August 2008
     * permission to use this function is granted provided
     * that this copyright notice is retained intact
     * @author Stephen Chapman
     * @link http://javascript.about.com/library/blnumfmt.htm
     * 
     * @param {Int} num
     * @param {Int} decimal places
     * @param {String} thousand separator
     * @param {String} decimal point
     * @param {String} front currency number
     * @param {String} back currency number
     * @param {String} front symbol
     * @param {String} back symbol
     */
    numberFormat: function(num, dec, thou, pnt, curr1, curr2, n1, n2) {
        var x = Math.round(num * Math.pow(10,dec));
        if (x >= 0) n1=n2='';
        
        var y = (''+Math.abs(x)).split('');
        var z = y.length - dec;
        
        if (z<0) z--;
        
        for(var i = z; i < 0; i++) {
            y.unshift('0');
        }
        
        y.splice(z, 0, pnt);
        if(y[0] == pnt) y.unshift('0');
        
        while (z > 3) {
            z-=3;
            y.splice(z,0,thou);
        }
        
        var r = curr1+n1+y.join('')+n2+curr2;
        
        return r;
    },
    
    initDatatable: function(tableId, scrollXInner) {
        jQuery("#" + tableId.toString()).dataTable({
            "bRetrieve": true,
            "bDestroy": true,
            "bPaginate": false,
            "bFilter": false,
            "bSort": false,
            "sScrollY": 280,
            "sScrollX": "100%",
            "sScrollYInner": 100,
            "sScrollXInner": scrollXInner.toString() + "%",
            "bScrollCollapse": true,
            "bJQueryUI": true,
            "oLanguage": {
                "sInfo": "",
                "sInfoFiltered": ""
            }
        });
    },
    
    initCalendar: function(datepickerId) {
        jQuery("#" + datepickerId.toString()).datepicker({
            hide: true,
            autoSize: true,
            yearRange: '-90:+0',
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        })
    },
    
    initFormDialog: function(elementId, dialogTitle, width, height) {
        jQuery("#" + elementId).dialog({
            title: dialogTitle,
            autoOpen: false,
            closeOnEscape: false,
            draggable: false,
            resizable: false,
            modal: true,
            width: width,
            height: height
        })
    },
    
    initDialog: function(elementId, dialogTitle, dialogWidth, dialogHeight) {
        jQuery("#" + elementId).dialog({
            title: dialogTitle,
            autoOpen: true,
            draggable: false,
            resizable: false,
            modal: true,
            width: dialogWidth,
            height: dialogHeight,
            buttons: {
                "OK": function() {
                    jQuery(this).dialog("close");
                }
            }
        })
    },
    
    initConfirmationDialog: function(elementId, dialogTitle, dialogWidth, dialogHeight, confirmFunction) {
        jQuery("#" + elementId).dialog({
            title: dialogTitle,
            autoOpen: false,
            draggable: false,
            resizable: false,
            modal: true,
            width: dialogWidth,
            height: dialogHeight,
            buttons: {
                "Yes": confirmFunction,
                "No": function() {
                    jQuery(this).dialog("close");
                }
            }
        })
    },
    
    openDialog: function(elementId) {
        jQuery("#" + elementId).dialog("open");
    },
    
    closeDialog: function(elementId) {
        jQuery("#" + elementId).dialog("close");
    },
    
    splitSerializeForm: function(formId) {
        var myForm = Form.serialize(formId.toString()).toString().split('&');
        var myArray = new Array();
        for (var i = 0; i < myForm.length; ++i) {
            var explode = myForm[i].toString().split('=');
            myArray[explode[0]] = explode[1];
        }
        return myArray;
    },
    
    /**
    * Function : dump()
    * Arguments: The data - array,hash(associative array),object
    *    The level - OPTIONAL
    * Returns  : The textual representation of the array.
    * This function was inspired by the print_r function of PHP.
    * This will accept some data as the argument and return a
    * text that will be a more readable version of the
    * array/hash/object that is given.
    * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
    */
    dump: function(arr, level) {
        var dumped_text = "";
        if(!level) level = 0;

        //The padding given at the beginning of the line.
        var level_padding = "";
        for(var j=0;j<level+1;j++) level_padding += "    ";

        if(typeof(arr) == 'object') { //Array/Hashes/Objects 
            for(var item in arr) {
                var value = arr[item];

                if(typeof(value) == 'object') { //If it is an array,
                    dumped_text += level_padding + "'" + item + "' ...\n";
                    dumped_text += dump(value,level+1);
                } else {
                    dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                }
            }
        } else { //Stings/Chars/Numbers etc.
            dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
        }
        return dumped_text;
    }
})