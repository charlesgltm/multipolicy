var PolicyType = Class.create({
    
    ajaxFile: 'PolicyType.php',
    id: null,
    policyTypeValid: false,
    genderValid: false,
    
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
                    Customer.updatePolicyType();
                }
                else {
                    var customerData = Customer.getCustomerDetails();
                    document.policyTabForm['policyType'].selectedIndex = customerData['package_id'];
                    PolicyType.setId(customerData['package_id']);
                    document.getElementById("policyDialog").innerHTML = '<b>Policy Type can not be empty.</b>';
                    Functions.initDialog("policyDialog", "Information", 300, 150);
                }
            }
        })
    },
    
    validate: function(action) {
        var policyTypeValid = false;
        if ([1, 2, 3].indexOf(parseInt(this.getId())) >= 0) {
            new Ajax.Request(this.getAjaxUrl(), {
                asynchronous: false,
                method: 'get',
                parameters: 'get=validate&action=' + action + '&custId=' + Customer.getId() + '&policyTypeId=' + this.getId(),
                onSuccess: function(response) {
                    if (response.responseText != '') {
                        document.getElementById("policyDialog").innerHTML = '<span style="font-weight: bold; color: #FF1437">' + response.responseText + '</span>';
                        Functions.initDialog("policyDialog", "Information", 350, 150);
                        policyTypeValid = false;
                    }
                    else {
                        policyTypeValid = true;
                    }
                }
            })
        }
        this.policyTypeValid = policyTypeValid;
    },
    
    validateGender: function() {
        var isValid = false;
        new Ajax.Request(this.getAjaxUrl(), {
            asynchronous: false,
            method: 'get',
            parameters: 'get=validateGender&custId=' + Customer.getId() + '&policyId=' + Policy.getId() + '&policyTypeId=' + this.getId(),
            onSuccess: function(response) {
                if (response.responseText != '') {
                    document.getElementById("policyDialog").innerHTML = '<span style="font-weight: bold; color: #FF1437">' + response.responseText + '</span>';
                    Functions.initDialog("policyDialog", "Information", 350, 150);
                    isValid = false;
                }
                else {
                    isValid = true;
                }
            }
        })
        this.genderValid = isValid;
    }
})