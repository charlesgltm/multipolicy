var Calculator = Class.create({
    
    ajaxFile: 'Calculator.php',
    
    getAjaxUrl: function() {
        return Functions.ajaxPath + '/' + this.ajaxFile;
    },
    
    getPremium: function(mode) {
        var formId, infoId, myForm, error = false;
        if (mode === 'add') {
            formId = "addPolicyForm";
            infoId = "addPolicyInformation";
            myForm = document.addPolicyForm;
        }
        else {
            formId = "updatePolicyForm";
            infoId = "updatePolicyInformation";
            myForm = document.updatePolicyForm;
        }
        
        var policyForm = Functions.splitSerializeForm(formId.toString()),
            gender = policyForm['policy_gender'],
            job = policyForm['policy_job'],
            pkg = policyForm['policy_package'];
        
        if (gender !== '' && job !== '' && pkg !== '') {
            if (Job.isAccepted()) {
                new Ajax.Request(this.getAjaxUrl(), {
                    method: 'get',
                    parameters: 'get=getPremium&gender=' + gender + '&job=' + job + '&package=' + pkg,
                    onSuccess: function(response) {
                        myForm['policy_premium'].value = response.responseText;
                        document.getElementById("policy_premium").innerHTML = Functions.numberFormat(response.responseText, 0, ',', '', 'Rp. ', '', '', '');
                        document.getElementById(infoId.toString()).innerHTML = '';
                        jQuery("#" + infoId.toString()).removeClass("error");
                    }
                });
            }
            else {
                document.getElementById(infoId.toString()).innerHTML = 'Sorry, this job is rejected.';
                jQuery("#" + infoId.toString()).addClass("error");
                jQuery("#" + infoId.toString()).css("text-align", "center");
                document.getElementById("policy_premium").innerHTML = 0;
            }
        }
        else {
            document.getElementById("policy_premium").innerHTML = 0;
        }
    },
    
    generateTotal: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: 'get=generateTotal&custId=' + Customer.getId(),
            onSuccess: function(response) {
                document.getElementById("premiumTotal").innerHTML = response.responseText;
            }
        })
    },
    
    generateInstallment: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: 'get=generateInstallment&custId=' + Customer.getId(),
            onSuccess: function(response) {
                document.getElementById("premiumInstallment").innerHTML = response.responseText;
            }
        })
    }
})