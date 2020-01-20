<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" type="text/css" href="css/styles.css" />

    <script src="/js/vendor/mustache.min.js"></script>

    <script src="/js/helpers.js"></script>
    <script src="/js/view.js"></script>
    <script src="/js/processData.js"></script>
    <script src="/js/main.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@7.2.0/dist/css/autoComplete.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@7.2.0/dist/js/autoComplete.min.js"></script>



    <script>

        window.addEventListener('DOMContentLoaded', (event) => {
            window.maidea.init();

            new autoComplete({
                data: {                              // Data src [Array, Function, Async] | (REQUIRED)
                    src: async () => {
                        // User search query
                        const query = document.querySelector("#autoComplete").value;
                        // Fetch External Data Source
                        const source = await fetch(`index.php?action=getCityAutocomplete&q=${query}`);
                        // Format data into JSON
                        const data = await source.json();
                        // Return Fetched data
                        console.log(data);

                        return data;
                    },
                    key: ["title"],
                    cache: false
                },

                resultsList: {
                    render: true
                },
                onSelection: function(feedback) {
                    //console.log(feedback);
                    window.location.href = 'index.php?cityId=' + feedback.selection.value.id
                },

            });

        });

    </script>


    <script>



    </script>


</head>
<body>

