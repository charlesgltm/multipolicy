
var PremiumCalculator = Class.create({
    
    ajaxFile: 'PremiumCalculator.php',
    displayNumOfCustomer: 1,
    numOfCustomer: 1,
    
    installment: new Array(1, 3, 6, 12),
    
    getAjaxUrl: function() {
        return Functions.ajaxPath + '/' + this.ajaxFile;
    },
    
    addNewCustomer: function() {
        this.numOfCustomer++;
        this.displayNumOfCustomer++;
        var numOfCustomer = this.numOfCustomer;
        var displayNumOfCustomer = this.displayNumOfCustomer;
        var params = 'get=addcustomer&num=' + numOfCustomer + '&displaynum=' + displayNumOfCustomer;
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: params,
            onSuccess: function(response) {
                jQuery("#list-customers").append(response.responseText);
                jQuery("#customer" + numOfCustomer).addClass("customers");
                PremiumCalculator.resetCustomerNumber();
            }
        })
    },
    
    removeCustomer: function(elementId) {
        this.displayNumOfCustomer--;
        jQuery(elementId).remove();
        this.resetCustomerNumber();
        this.calculateTotal();
    },
    
    resetCustomerNumber: function() {
        var formname = document.premiumcalculator;
        var tmpNum = this.displayNumOfCustomer;
        
        for (var i = 1; i < tmpNum; i++) {
            formname['displaynumber[]'][i].value = (i + 1) + '.';
        }
        
        var startNum = 1;
        for (var j = 1; j <= this.numOfCustomer; ++j) {
            customerId = document.getElementById("hiddenNum" + j);
            if (customerId == null) {
                continue;
            }
            document.getElementById("hiddenNum" + j).innerHTML = startNum;
            startNum++;
        }
    },
    
    calculate: function(custNo, custId) {
        var formname = document.premiumcalculator;
        var genderCode = null;
        var jobType = null;
        var packageId = null;
        var premiumCost = null;
        var decrement = custNo - 1;
        var params = null;
        
        if (this.displayNumOfCustomer > 1) {
            genderCode = formname['gender_code[]'][decrement].value;
            jobType = formname['job[]'][decrement].value;
            packageId = formname['packages[]'][decrement].value;
            premiumCost = formname['premiumCost[]'][decrement];
        }
        else {
            genderCode = formname['gender_code[]'].value;
            jobType = formname['job[]'].value;
            packageId = formname['packages[]'].value;
            premiumCost = formname['premiumCost[]'];
        }
        
        if (genderCode !== '' && jobType !== '' && packageId !== '') {
            params = 'get=calculate&gender_code=' + genderCode + '&job=' + jobType + '&packages=' + packageId;
            new Ajax.Request(this.getAjaxUrl(), {
                method: 'get',
                parameters: params,
                onSuccess: function (response) {
                    premiumCost.readonly = false;
                    premiumCost.value = Functions.numberFormat(response.responseText, 0, '.', '', '', '', '', '');
                    premiumCost.readonly = true;
                    PremiumCalculator.checkAcceptance(custNo, custId, jobType);
                    PremiumCalculator.calculateTotal();
                }
            })
        }
        else {
            premiumCost.readonly = false;
            premiumCost.value = 0;
            premiumCost.readonly = true;
            PremiumCalculator.calculateTotal();
        }
    },
    
    checkAcceptance: function(custNo, custId, job) {
        var params = 'get=checkacceptance&job=' + job;
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: params,
            onSuccess: function(response) {
                var icon = (response.responseText == 'A') ? 'icon-accepted.gif' : 'icon-denied.gif';
                document.getElementById("iconCustomer" + custId).innerHTML = '<img src="./tpl/img/' + icon + '" align="absmiddle" />';
                if (response.responseText !== 'A') {
                    alert("Sorry, Job Type for Customer #" + custNo + " is not allowed");
                }
            }
        })
    },
    
    setFeePolicy: function() {
        var params = 'get=feepolicy';
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: params,
            asynchronous: false,
            onSuccess: function(response) {
                document.getElementById("feepolicy").innerHTML = Functions.numberFormat(response.responseText, 0, '.', '', '', '', '', '');
            }
        })
    },
    
    getFeePolicy: function() {
        var cost = document.getElementById("feepolicy").innerHTML;
        return cost;
    },
    
    setFeeStamp: function(total) {
        var params = 'get=feestamp&total=' + total;
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: params,
            asynchronous: false,
            onSuccess: function(response) {
                document.getElementById("feestamp").innerHTML = Functions.numberFormat(response.responseText, 0, '.', '', '', '', '', '');
            }
        })
    },
    
    getFeeStamp: function() {
        var cost = document.getElementById("feestamp").innerHTML;
        return cost;
    },
    
    setFeeAdmin: function() {
        var params = 'get=feeadmin';
        new Ajax.Request(this.getAjaxUrl(), {
            method: 'get',
            parameters: params,
            asynchronous: false,
            onSuccess: function(response) {
                document.getElementById("feeadmin").innerHTML = Functions.numberFormat(response.responseText, 0, '.', '', '', '', '', '');
            }
        })
    },
    
    getFeeAdmin: function() {
        var cost = document.getElementById("feeadmin").innerHTML;
        return cost;
    },
    
    calculateDiscount: function() {
        var formname = document.premiumcalculator;
        var discount = parseInt(formname['discount'].value);
        var subtotal = document.getElementById("subtotal").innerHTML;
        
        document.getElementById("displaydiscount").innerHTML = discount;
        return (discount / 100) * parseInt(subtotal.replace(RegExp(/[^0-9]/g), ''));
    },
    
    calculateTotal: function() {
        var formname = document.premiumcalculator;
        var premiumCost = 0;
        var subtotal = 0;
        var total = 0;
        var grandtotal = 0;
        
        for (var i = 0; i < this.displayNumOfCustomer; ++i) {
            premiumCost = (this.displayNumOfCustomer > 1) ? formname['premiumCost[]'][i].value : formname['premiumCost[]'].value;
            subtotal += parseInt(premiumCost.replace(RegExp(/[^0-9]/g), ''));
        }
        document.getElementById("subtotal").innerHTML = Functions.numberFormat(subtotal, 0, '.', '', '' ,'', '', '');
        document.getElementById("discount").innerHTML = Functions.numberFormat(this.calculateDiscount(), 0, '.', '', '', '', '', '');
        
        total = subtotal - parseInt(this.calculateDiscount());
        document.getElementById("total").innerHTML = Functions.numberFormat(total, 0, '.', '', '', '', '', '');
        
        this.setFeePolicy();
        this.setFeeStamp(total);
        this.setFeeAdmin();
        
        var feePolicy = this.getFeePolicy().replace(RegExp(/[^0-9]/g), '');
        var feeStamp = this.getFeeStamp().replace(RegExp(/[^0-9]/g), '');
        var feeAdmin = this.getFeeAdmin().replace(RegExp(/[^0-9]/g), '');
        
        if (total > 0) {
            grandtotal = (parseInt(feePolicy) + parseInt(feeStamp) + parseInt(feeAdmin)) + total;
            document.getElementById("grandtotal").innerHTML = Functions.numberFormat(grandtotal, 0, '.', '', '', '', '', '');
        }
        
        this.calculateInstallment();
    },
    
    getGrandTotal: function() {
        var grandTotal = document.getElementById("grandtotal").innerHTML;
        return grandTotal;
    },
    
    calculateInstallment: function() {
        var installment = 0;
        var firstInstallment = 0;
        var nextInstallment = 0;
        var feePolicy = parseInt(this.getFeePolicy().replace(RegExp(/[^0-9]/g), ''));
        var feeStamp = parseInt(this.getFeeStamp().replace(RegExp(/[^0-9]/g), ''));
        var feeAdmin = parseInt(this.getFeeAdmin().replace(RegExp(/[^0-9]/g), ''));
        var allFee = feePolicy + feeStamp + feeAdmin;
        var total = parseInt(this.getGrandTotal().replace(RegExp(/[^0-9]/g), '')) - allFee;
        
        if (total > 0) {
            this.installment.each(function(value) {
                installment = total / value;
                
                firstInstallment = installment + allFee;
                nextInstallment = (value == 1) ? 0 : installment;
                
                document.getElementById("firstInstallment" + value).innerHTML = Functions.numberFormat(firstInstallment, 0, '.', '', 'Rp. ', '', '', '');
                document.getElementById("nextInstallment" + value).innerHTML = Functions.numberFormat(nextInstallment, 0, '.', '', 'Rp. ', '', '', '');
            })
        }
    }
})