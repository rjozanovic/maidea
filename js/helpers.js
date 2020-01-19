window.maidea = (function(m){

    m.helpers = m.helpers || {};

    m.helpers.addClass = function(element, className){
        var classes = element.className.split(' ');
        if(classes.indexOf(className) === -1){
            classes.push(className);
            element.className = classes.join(' ');
        }
    };

    m.helpers.removeClass = function(element, className){
        var classes = element.className.split(' ');
        var idxToRemove = classes.indexOf(className);
        if(idxToRemove !== -1){
            classes.splice(idxToRemove, 1);
            element.className = classes.join(' ');
        }
    };

    m.helpers.hasClass = function(element, className){
        return element.className.indexOf(className) !== -1;
    };

    //searches trought anscestry untill first element of that tag name found
    m.helpers.closestWithTagName = function(element, tagName){
        var starting = element;
        tagName = tagName.trim().toUpperCase();
        while(typeof element.parentElement !== 'undefined' && element.parentElement !== null && (element.tagName !== tagName || element === starting))
            element = element.parentElement;
        return element.tagName === tagName ? element : undefined;
    };

    //searches trought anscestry untill first element of that tag name found
    m.helpers.closestWithClassName = function(element, className){
        var starting = element;
        while(typeof element.parentElement !== 'undefined' && element.parentElement !== null && (!m.helpers.hasClass(element, className) || element === starting))
            element = element.parentElement;
        return m.helpers.hasClass(element, className) ? element : undefined;
    };

    //returns dom fragment
    m.helpers.parseHtmlString = function(htmlString){
        var tmp = document.createElement('div');
        tmp.innerHTML = htmlString;
        var newNodes = Array.prototype.slice.call(tmp.childNodes);
        var frag = document.createDocumentFragment();
        for(var i = 0 ; i < newNodes.length ; i++)
            frag.appendChild(tmp.removeChild(newNodes[i]));
        return frag;
    }

    //appends html string to element by creating temporary element to parse string, transfer nodes to document fragment and append to element.
    m.helpers.appendHtmlString = function(htmlString, element){
        element.appendChild(m.helpers.parseHtmlString(htmlString));
    };

    m.helpers.emptyElement = function(container, keepEles){
        keepEles = keepEles || [];
        var children = Array.prototype.slice.call(container.childNodes);
        for(var i in children){
            if(keepEles.indexOf(children[i]) === -1)
                container.removeChild(children[i]);
        }
        return children;
    };

    //@note not recursive atm
    m.helpers.mergeObjects = function(){
        var ret = {};
        var players = Array.prototype.slice.call(arguments);
        for(var i in players){
            var obj = players[i];
            for(var key in obj){
                if(obj.hasOwnProperty(key))
                    ret[key] = obj[key];
            }
        }
        return ret;
    };


    m.helpers.intToTwoDigit = function(int){
        return ("0" + int).slice(-2);
    };

    m.helpers.formatDate = function(date, includeSeconds){
        includeSeconds = includeSeconds || true;
        var day = m.helpers.intToTwoDigit(date.getDate());
        var month = m.helpers.intToTwoDigit(date.getMonth() + 1);     //0-11
        var year = date.getFullYear();
        var hours = m.helpers.intToTwoDigit(date.getHours());
        var minutes = m.helpers.intToTwoDigit(date.getMinutes());
        var seconds = m.helpers.intToTwoDigit(date.getSeconds());
        return day + '.' + month + '.' + year + '. ' + hours + ':' + minutes + (includeSeconds ? (':' + seconds) : '');
    };

    return m;

})(window.maidea || {});