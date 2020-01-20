window.maidea = (function(m){

    m.ajax = m.ajax || {};

    m.ajax.get = function(url, callback, parseJson, errorCallback) {

        parseJson = typeof parseJson === 'boolean' ? parseJson : true;

        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                callback((parseJson ? JSON.parse(this.responseText) : this.responseText), this);
            }
        };

        if(typeof errorCallback === 'function')
            xhttp.onerror = errorCallback;

        xhttp.open("POST", url, true);
        xhttp.send();

    };

    return m;

})(window.maidea || {});