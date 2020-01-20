window.maidea = (function(m){

    function render(name, data){
        var template = m.view.getTemplate(name);
        var data = m.view.processData.all(data);
        console.log(data);
        var renderStr = m.view.renderTemplate(template, data);
        var frag = m.helpers.parseHtmlString(renderStr);
        return frag;
    }

    function processChange(name, data, target){
        var frag = render(name, data);
        target.parentNode.replaceChild(frag, target);
        var addFav = document.getElementById('addToFavorites');     //TODO
        if(name === 'weather' && addFav){
            addFav.addEventListener('click', function(ev){
                ev.preventDefault();
                m.ajax.get(addFav.getAttribute('href'), function(){window.location.reload();}, false);
            });
        }
        if(data.hasOwnProperty('reload') && data.reload.hasOwnProperty('inTime')){
            setTimeout(function(){
                m.ajax.get(data.reload.dataUrl, function(resp){
                    processChange(name, resp, document.getElementById('out-'+name));
                }, true);
            }, data.reload.inTime * 1000);
        }
    }

    m.init = function(){

        m.view.registerPartialTemplates();

        for(var i in window.maidea_renderRequests){
            var item = window.maidea_renderRequests[i];
            processChange(item.name, item.data, document.getElementById(item.scriptId));
        }

    };

    return m;

})(window.maidea || {});