window.maidea = (function(m){

    m.view = m.view || {};
    m.view.processData = m.view.processData || {videos: {}, config: {}};

    m.view.processData.all = function(data, pkColumnName){

        //function for date formatting
        data['formatDate'] = function(){
            return function(val, render){
                val = render(val);
                if(val){
                    var d = new Date(val * 1000);
                    return m.helpers.formatDate(d);
                }
            };
        };

        return data;
    };

    return m;

})(window.maidea || {});