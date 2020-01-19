window.maidea = (function(m){

    m.view = m.view || {};

    m.view.renderTemplate = function(template, data){
        Mustache.parse(template);
        return Mustache.render(template, data, m.view.partialTemplates);
    };

    m.view.getPagerHtml = function(data){
        return m.view.renderTemplate(m.view.getTemplate('pager'), data);
    };

    m.view.getModalHtml = function(data){
        return m.view.renderTemplate(m.view.getTemplate('modal'), data);
    };

    m.view.getLoginFormHtml = function(){
        return m.view.renderTemplate(m.view.getTemplate('login-form'), {});
    };

    m.view.getTemplate = function(name){
        return document.getElementById('tpl-' + name).innerHTML;
    };

    m.view.registerPartialTemplates = function(){
        var partialEls = document.getElementsByClassName('tpl-partial');
        var partials = {};
        for(var i = 0 ; i < partialEls.length ; i++)
            partials[partialEls.item(i).id] = partialEls.item(i).innerHTML;
        m.view.partialTemplates = partials;
    };

    return m;

})(window.maidea || {});