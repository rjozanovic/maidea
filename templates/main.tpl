
<div class="wrapper">
    <div class="sidebar">
        {{#favoriteCities}}
            <div class="favorite-city">
                <a href="index.php?cityId={{id}}">{{name}}</a>
            </div>
        {{/favoriteCities}}
    </div>

    <div class="weather-info">

        <div class="weather-now">
            {{#weather}}
                {{#data}}
                    <b>Current weather in {{name}}</b><hr>
                    {{#weather}}
                        {{#.}}
                            {{main}}
                        {{/.}}
                    {{/weather}}
                {{/data}}
            {{/weather}}
            <hr>
        </div>

        <div class="weather-forecast">
            {{#forecasts}}
                {{#data}}
                    <div class="forecast-item">
                        {{#formatDate}}{{dt}}{{/formatDate}}
                        {{#weather}}
                            {{#.}}
                                {{main}}
                            {{/.}}
                        {{/weather}}
                    </div>
                {{/data}}
            {{/forecasts}}
        </div>


    </div>
</div>