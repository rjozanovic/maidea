window.maidea = (function(m){

    m.view = m.view || {};
    m.view.processData = m.view.processData || {videos: {}, config: {}};

    /**
     * Converts object to an
     * array of object items with key and value properties. Also adds pk value.
     *
     * Usefull for mustache when keys are unknown or can be dinamically added
     * since mustache only allows looping trough arrays not objects.
     *
     * @param obj
     */
    m.view.processData.associativeObjToArray = function(obj, pkColumnName){

        function processObj(obj){
            var ret = [];
            var pkValue = undefined;
            for(var i in obj){
                if(i === pkColumnName)
                    pkValue = obj[i];
                if(obj.hasOwnProperty(i)){
                    var columnInfo = {key: i, value: obj[i]};
                    columnInfo['_column_' + i] = obj[i];
                    ret.push(columnInfo);
                }
            }
            if(typeof pkValue !== 'undefined'){
                for(var j in ret)
                    ret[j]['pkValue'] = pkValue;
            }
            return ret;
        }

        if(Array.isArray(obj)){     //array of objects do for each
            var ret = [];
            for(var j = 0 ; j < obj.length ; j++)
                ret.push(processObj(obj[j]));
        } else
            ret = processObj(obj);

        return ret;
    };

    m.view.processData.all = function(data, pkColumnName){

        //function for date formatting
        data['formatDate'] = function(){
            return function(val, render){
                val = render(val);
                if(val){
                    var d = new Date(val);
                    return m.helpers.formatDate(d);
                }
            };
        };

        data['arrayNotation'] = m.view.processData.associativeObjToArray(data['data'], pkColumnName);

        return data;
    };

    return m;

})(window.maidea || {});