window.maidea = (function(m){

    var lastRendered = {};

    function render(name, data){
        var template = m.view.getTemplate(name);
        var data = m.view.processData.all(data);
        console.log(data);
        var renderStr = m.view.renderTemplate(template, data);
        var frag = m.helpers.parseHtmlString(renderStr);
        return frag;
    }

    m.init = function(){

        m.view.registerPartialTemplates();

        for(var i in window.maidea_renderRequests){

            (function(){
                var item = window.maidea_renderRequests[i];

                var frag = render(item.name, item.data);

                var script = document.getElementById(item.scriptId);
                script.parentNode.replaceChild(frag, script);

                /*lastRendered[item.name] = {item: item, target: frag.firstElementChild};

                if(item.data.hasOwnProperty('reload') && item.data.reload.hasOwnProperty('inTime')){
                    setTimeout(function(){
                        m.ajax.get(item.data.reload.dataUrl, function(resp){

                        }, true);
                    }, item.data.reload.inTime * 1000);
                }*/
            })();

        }

        console.log(lastRendered);

    };

    return m;

})(window.maidea || {});