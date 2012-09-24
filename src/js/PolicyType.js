var PolicyType = Class.create({
    
    ajaxFile: 'PolicyType.php',
    id: null,
    policyTypeValid: false,
    
    getAjaxUrl: function() {
        return Functions.ajaxPath + '/' + this.ajaxFile
    },
    
    setId: function(id) {
        this.id = id
    },
    
    getId: function() {
        return this.id;
    },
    
    getRules: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            asynchronous: false,
            method: 'get',
            parameters: 'get=rules&policyTypeId=' + this.getId(),
            onSuccess: function(response) {
                if (PolicyType.getId() != '') {
                    document.getElementById("policyDialog").innerHTML = response.responseText;
                    Functions.initDialog("policyDialog", "Information", 300, 200);
                }
                else {
                    document.getElementById("policyDialog").innerHTML = 'Policy Type can not be empty.';
                    Functions.initDialog("policyDialog", "Information", 300, 150);
                }
            }
        })
    },
    
    validate: function() {
        var policyTypeValid = false;
        new Ajax.Request(this.getAjaxUrl(), {
            asynchronous: false,
            method: 'get',
            parameters: 'get=validate&custId=' + Customer.getId() + '&policyTypeId=' + this.getId(),
            onSuccess: function(response) {
                if (response.responseText != '') {
                    document.getElementById("policyDialog").innerHTML = '<span style="font-weight: bold; color: #FF1437">' + response.responseText + '</span>';
                    Functions.initDialog("policyDialog", "Information", 320, 200);
                    policyTypeValid = false;
                }
                else {
                    policyTypeValid = true;
                }
            }
        })
        this.policyTypeValid = policyTypeValid;
    }
})