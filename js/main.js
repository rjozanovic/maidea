window.maidea = (function(m){



    m.init = function(){

        for(var i in window.maidea_renderRequests){
            var item = window.maidea_renderRequests[i];

            console.log(item);

            var template = m.view.getTemplate(item.name);
            var data = m.view.processData.all(item.data);

            console.log(template);
            console.log(data);

            var renderStr = m.view.renderTemplate(template, data);

            var frag = m.helpers.parseHtmlString(renderStr);


            var script = document.getElementById(item.scriptId);
            script.parentNode.replaceChild(frag, script);

        }

    };

    return m;

})(window.maidea || {});