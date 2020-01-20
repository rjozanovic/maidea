window.maidea = (function(m){



    m.init = function(){

        m.view.registerPartialTemplates();

        for(var i in window.maidea_renderRequests){
            var item = window.maidea_renderRequests[i];

            console.log(item);

            var template = m.view.getTemplate(item.name);
            var data = m.view.processData.all(item.data);

            //console.log(template);
            console.log(data);


            if(data['weather']['upToDate'] === false || data['forecasts']['isComplete'] === false){

                console.log('WILL RELOAD ' + (data['weather']['upToDate'] === false ? ' weather ' : '') + (data['forecasts']['isComplete'] === false ? ' forecasts ' : ''));
                setTimeout(function(){
                    window.location.reload();
                }, 10000);

            }

            var renderStr = m.view.renderTemplate(template, data);

            var frag = m.helpers.parseHtmlString(renderStr);


            var script = document.getElementById(item.scriptId);
            script.parentNode.replaceChild(frag, script);

        }

    };

    return m;

})(window.maidea || {});