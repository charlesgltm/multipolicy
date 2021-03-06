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
            parameters: 'get=customerDetails&custId=' + this.getId(),
            onSuccess: function(response) {
                data = response.responseText;
            }
        });
        return(data.evalJSON());
    },
    
    update: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'post',
            parameters: Form.serialize("updateCustomerForm"),
            onSuccess: function(response) {
                var info = '';
                if (response.responseText == 'true') {
                    info = 'Customer successfully saved';
                }
                else {
                    info = 'Failed to save Customer info.<br />Please try again.';
                }
                document.getElementById("customerInformation").innerHTML = '<b>' + info + '</b>';
                Functions.initDialog("customerInformation", "Information", 220, 150);
            }
        })
    },
    
    updatePolicyType: function() {
        PolicyType.validate('change');
        if (PolicyType.policyTypeValid) {
            new Ajax.Request(this.getAjaxUrl(), {
                method: 'post',
                parameters: 'act=updatePolicyType&custId=' + Customer.getId() + '&policyTypeId=' + PolicyType.getId(),
                onSuccess: function() {
                    Customer.updateCost();
                    Policy.listPolicy();
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