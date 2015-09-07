if (typeof(bsd) == 'undefined') {
    bsd = {}
}
if (typeof(bsd.tr) == 'undefined') {
        bsd.tr = {};
}
bsd.at = function() {
    this._init();
}

bsd.at.prototype = {

    _init: function() {
        var self = this;
        self.app = window.location.hostname;
        self.app_user_id = '' // TODO get guid cookie
    },
            
    _setupParams: function(name, args) {
        var self = this;

        var params = {};
        params.a = name;
        params.c = self.client;

        if(args.attributedAction)
            params.attributedAction = args.attributedAction;
        if(args.guid)
            params.g = args.guid;
        else if(jaaulde.utils.cookies.get('guid') != null) {
            params.g = jaaulde.utils.cookies.get('guid');
        }

        if(args.sourceCodes) {
            for(key in args.sourceCodes) {
                params['sourceCodes['+key+']'] = args.sourceCodes[key];
            }
        }
        if(args.attributes) {
            for(key in args.attributes) {
                params['attributes['+key+']'] = args.attributes[key];
            }
        }
        
        return params;
    },

    setGuid: function(callback) {
        JSONP.get(self.baseUrl + '/setGuid.php', params, function(data) {
            if(jaaulde.utils.cookies.get('guid') == null) {
                jaaulde.utils.cookies.set('guid', data.guid, {domain: data.domain});
            }
            callback(data);
        });
    },

    add: function(name, args, callback) {
        var self = this;

        var params = self._setupParams(name, args);

        JSONP.get(self.baseUrl + '/add.php', params, function(data) {
            if(!params.g) {
                jaaulde.utils.cookies.set('guid', data.guid, {domain: data.domain});
            }
            callback(data);
        });
    },
            
    addUnique: function(name, args, callback) {
        var self = this;

        var params = self._setupParams(name, args);
        
        if(args.naturalKey) {
            for(key in args.naturalKey) {
                for(innerkey in args.naturalKey[key]) {
                    params['nk['+key+']['+innerkey+']'] = args.naturalKey[key][innerkey];
                }
            }
        }

        JSONP.get(self.baseUrl + '/addUnique.php', params, function(data) {
            if(!params.g) {
                jaaulde.utils.cookies.set('guid', data.guid, {domain: data.domain});
            }
            callback(data);
        });
    },

    getByGuid: function(args, callback) {
        var self = this;

        var params = {};
        params.c = self.client;
        if(args.guid) {
            params.g = args.guid;
        }
        JSONP.get(self.baseUrl + '/get.php', params, function(data) {
            callback(data);
        });
    },

    getGraph: function(args, callback) {
        var self = this;

        var params = {};
        params.c = self.client;
        if(args.guid) {
            params.g = args.guid;
        }
        if(args.attributedAction) {
            params.a = args.attributedAction;
        }
        JSONP.get(self.baseUrl + '/getGraph.php', params, function(data) {
            callback(data);
        });
    }
}


