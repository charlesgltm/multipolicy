var Customer = Class.create ({
    
    ajaxFile: 'Customer.php',
    id: null,
    
    getAjaxUrl: function() {
        return Functions.ajaxPath + '/' + this.ajaxFile;
    },
    
    setId: function(id) {
        this.id = id;
    },
    
    getId: function() {
        return this.id;
    },
    
    getCustomerDetails: function() {
        var data = null;
        new Ajax.Request(this.getAjaxUrl(), {
            asynchronous: false,
            method: 'get',
            parameters: 'get=customerDetails&custId=' + Customer.getId(),
            onSuccess: function(response) {
                data = response.responseText;
            }
        });
        return(data.evalJSON());
    },
    
    updatePolicyType: function() {
        PolicyType.validate('change');
        if (PolicyType.policyTypeValid) {
            new Ajax.Request(this.getAjaxUrl(), {
                method: 'post',
                parameters: 'act=updatePolicyType&custId=' + Customer.getId() + '&policyTypeId=' + PolicyType.getId(),
                onSuccess: function() {
                    Customer.updateCost();
                    Policy.getPolicy();
                }
            })
        }
        else {
            var customerData = Customer.getCustomerDetails();
            document.policyTabForm['policyType'].selectedIndex = customerData['package_id'];
            PolicyType.setId(customerData['package_id']);
        }
    },
    
    updateCost: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'post',
            parameters: 'act=updateCost&custId=' + Customer.getId()
        })
    },
    
    updateInstallment: function(installment) {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'post',
            parameters: 'act=updateInstallment&custId=' + Customer.getId() + '&installment=' + installment
        })
    }
})