var Job = Class.create({
    
    ajaxFile: 'Job.php',
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
    
    isAccepted: function() {
        var accepted;
        new Ajax.Request(this.getAjaxUrl(), {
            asynchronous: false,
            method: 'get',
            parameters: 'get=acceptance&jobId=' + Job.getId(),
            onSuccess: function(response) {
                if (response.responseText === 'A')
                    accepted = true;
                else
                    accepted = false;
            }
        })
        return accepted;
    }
})