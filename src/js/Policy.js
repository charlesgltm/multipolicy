var Policy = Class.create({
    
    ajaxFile: 'Policy.php',
    id: null,
    totalPolicy: 0,
    
    getAjaxUrl: function() {
        return Functions.ajaxPath + '/' + this.ajaxFile;
    },
    
    setId: function(id) {
        this.id = id;
    },
    
    getId: function() {
        return this.id;
    },
    
    getPolicyDetails: function() {
        var data = null;
        new Ajax.Request(this.getAjaxUrl(), {
            asynchronous: false,
            method: 'get',
            parameters: 'get=policyDetails&policyId=' + this.getId(),
            onSuccess: function(response) {
                data = response.responseText;
            }
        });
        return(data.evalJSON());
    },
    
    activatingTab: function() {
        if (typeof Calculator !== 'object')
            Calculator = new Calculator;
        if (typeof PolicyType !== 'object')
            PolicyType = new PolicyType;
        if (typeof Job !== 'object')
            Job = new Job;
        
        var customer = Customer.getCustomerDetails();
        PolicyType.setId(customer['package_id']);
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: 'get=activatingTab&custId=' + Customer.getId(),
            onSuccess: function(response) {
                document.getElementById("tabPolicy").innerHTML = response.responseText;
                Functions.initFormDialog("addPolicyFormDialog", "Policy Form", 400, 510);
                jQuery("#new-policy-button").click(function() {
                    if (PolicyType.getId() == '' || PolicyType.getId() == null) {
                        document.getElementById("policyInformation").innerHTML = 'Please choose Policy Type first';
                        jQuery("#policyInformation").addClass("error");
                        jQuery("#policyInformation").css("font-weight", "bold");
                        jQuery("#policyInformation").css("text-align", "center");
                        jQuery("#policyInformation").fadeOut(5000, function() {
                            document.getElementById("policyInformation").innerHTML = '';
                            jQuery("#policyInformation").css("display", "block");
                            jQuery("#policyInformation").removeClass("info");
                            jQuery("#policyInformation").removeClass("error");
                        })
                    }
                    else {
                        Policy.calculateTotalPolicy();
                        PolicyType.validate('add');
                        if (PolicyType.policyTypeValid) {
                            jQuery("#addPolicyFormDialog").dialog("open");
                            Policy.loadPolicyForm();
                        }
                    }
                });
                Policy.listPolicy();
                Functions.initConfirmationDialog("dialogDeletePolicy", "Delete Policy", 330, 160, function() {
                    Policy.del();
                });
            }
        })
    },
    
    calculateTotalPolicy: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            asynchronous: false,
            method: 'get',
            parameters: 'get=totalPolicy&custId=' + Customer.getId(),
            onComplete: function(response) {
                Policy.setTotalPolicy(response.responseText);
            }
        });
    },
    
    setTotalPolicy: function(total) {
        this.totalPolicy = total;
    },
    
    getTotalPolicy: function() {
        return this.totalPolicy;
    },
    
    listPolicy: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: 'get=listPolicy&custId=' + Customer.getId(),
            onLoading: function() {
                document.getElementById("listPolicy").innerHTML = '<p class="info" style="text-align: center">Getting customer data</p>';
            },
            onSuccess: function(response) {
                document.getElementById("listPolicy").innerHTML = response.responseText;
                Functions.initDatatable("policyDataTable", 100);
                Functions.initFormDialog("updatePolicyFormDialog", "Policy Form", 400, 510);
                Calculator.generateTotal();
                Calculator.generateInstallment();
            }
        })
    },
    
    loadPolicyForm: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: 'get=loadPolicyForm&custId=' + Customer.getId() + '&policyType=' + PolicyType.getId(),
            onSuccess: function(response) {
                document.getElementById("updatePolicyFormDialog").innerHTML = '';
                document.getElementById("addPolicyFormDialog").innerHTML = response.responseText;
                Functions.initCalendar("newDob");
                jQuery(".input-text").keyup(function() {
                    Functions.textToUpper(this);
                });
            }
        })
    },
    
    validateAdd: function() {
        var policyForm = Functions.splitSerializeForm("addPolicyForm"),
            errorInfo = '';
        
        if (policyForm['policy_package'] === '') {
            errorInfo = 'Please choose policy package';
        }
        
        if (policyForm['policy_job'] === '') {
            errorInfo = 'Please choose policy job';
        }
        
        if (!Job.isAccepted()) {
            errorInfo = 'Job is rejected.';
        }
        
        if (policyForm['policy_gender'] === '') {
            errorInfo = 'Please choose policy gender';
        }
        
        if (policyForm['policy_relationship'] === '') {
            errorInfo = 'Please choose policy relationship to customer';
        }
        
        if (policyForm['policy_name'] === '') {
            errorInfo = 'Please input policy name';
        }
        
        if (errorInfo === '') {
            jQuery("#addPolicyInformation").removeClass("error");
            document.getElementById("addPolicyInformation").innerHTML = '';
            Policy.add();
        }
        else {
            document.getElementById("addPolicyInformation").innerHTML = errorInfo;
            jQuery("#addPolicyInformation").addClass("error");
        }
        jQuery("#addPolicyInformation").css("text-align", "center");
    },
    
    add: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'post',
            parameters: Form.serialize("addPolicyForm"),
            onLoading: function() {
                document.getElementById("addPolicyInformation").innerHTML = '<p class="info" style="text-align: center">Please Wait...</p>';
            },
            onSuccess: function(response) {
                if (response.responseText === 'true') {
                    document.getElementById("addPolicyInformation").innerHTML = 'New policy successfully added.';
                    jQuery("#addPolicyInformation").addClass("info");
                    setTimeout(function() {
                        jQuery("#addPolicyFormDialog").dialog('close');
                    }, 2000);
                    Customer.updateCost();
                    Policy.listPolicy();
                }
                else {
                    document.getElementById("addPolicyInformation").innerHTML = 'Failed to add new policy. Please try again.';
                    jQuery("#addPolicyInformation").addClass("error");
                }
            }
        })
    },
    
    openUpdatePolicyForm: function() {
        jQuery("#updatePolicyFormDialog").dialog("open");
        Policy.loadUpdateForm();
    },
    
    loadUpdateForm: function() {
        var policyData = this.getPolicyDetails();
        Job.setId(policyData['job_id']);
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: 'get=loadUpdateForm&policyId=' + Policy.getId(),
            onSuccess: function(response) {
                document.getElementById("addPolicyFormDialog").innerHTML = '';
                document.getElementById("updatePolicyFormDialog").innerHTML = response.responseText;
                Functions.initCalendar("updateDob");
                jQuery(".input-text").keyup(function() {
                    Functions.textToUpper(this);
                });
            }
        })
    },
    
    validateUpdate: function() {
        var policyForm = Functions.splitSerializeForm("updatePolicyForm"),
            errorInfo = '';
            
        if (policyForm['policy_package'] === '') {
            errorInfo = 'Please choose policy package';
        }
        
        if (policyForm['policy_job'] === '') {
            errorInfo = 'Please choose policy job';
        }
        
        if (!Job.isAccepted()) {
            errorInfo = 'Job is rejected.';
        }
        
        if (policyForm['policy_gender'] === '') {
            errorInfo = 'Please choose policy gender';
        }
        
        if (policyForm['policy_relationship'] === '') {
            errorInfo = 'Please choose policy relationship to customer';
        }
        
        if (policyForm['policy_name'] === '') {
            errorInfo = 'Please input policy name';
        }
        
        if (errorInfo === '') {
            jQuery("#updatePolicyInformation").removeClass("error");
            document.getElementById("updatePolicyInformation").innerHTML = '';
            Policy.update();
        }
        else {
            document.getElementById("updatePolicyInformation").innerHTML = errorInfo;
            jQuery("#updatePolicyInformation").addClass("error");
        }
        jQuery("#updatePolicyInformation").css("text-align", "center");
    },
    
    update: function() {
        var policyForm = Functions.splitSerializeForm("updatePolicyForm");
        PolicyType.validateGender(policyForm['policy_gender']);
        if (PolicyType.genderValid) {
            new Ajax.Request(this.getAjaxUrl(), {
                method: 'post',
                parameters: Form.serialize("updatePolicyForm"),
                onSuccess: function(response) {
                    if (response.responseText === 'true') {
                        document.getElementById("updatePolicyInformation").innerHTML = 'Policy successfully updated.';
                        jQuery("#updatePolicyInformation").addClass("info");
                        setTimeout(function() {
                            jQuery("#updatePolicyFormDialog").dialog('close');
                        }, 2000);
                        Customer.updateCost();
                        Policy.listPolicy();
                    }
                    else {
                        document.getElementById("updatePolicyInformation").innerHTML = 'Failed to update policy. Please try again.';
                        jQuery("#updatePolicyInformation").addClass("error");
                    }
                }
            })
        }
    },
    
    del: function() {
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'post',
            parameters: 'act=delete&policyId=' + Policy.getId(),
            onSuccess: function(response) {
                Functions.closeDialog("dialogDeletePolicy");
                jQuery("#policyInformation").css("text-align", "center");
                
                if (response.responseText === 'true') {
                    Customer.updateCost();
                    Policy.listPolicy();
                    document.getElementById("policyInformation").innerHTML = 'Policy was deleted.';
                    jQuery("#policyInformation").addClass("info");
                }
                else {
                    document.getElementById("policyInformation").innerHTML = 'Failed to delete policy data. Please try again';
                    jQuery("#policyInformation").addClass("error");
                }
                
                jQuery("#policyInformation").fadeOut(8000, function() {
                    document.getElementById("policyInformation").innerHTML = '';
                    jQuery("#policyInformation").css("display", "block");
                    jQuery("#policyInformation").removeClass("info");
                    jQuery("#policyInformation").removeClass("error");
                })
            }
        })
    }
})