<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" type="text/css" href="css/styles.css" />

    <script src="/js/vendor/mustache.min.js"></script>

    <script src="/js/helpers.js"></script>
    <script src="/js/view.js"></script>
    <script src="/js/ajax.js"></script>
    <script src="/js/processData.js"></script>
    <script src="/js/main.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="css/autoComplete.css">
    <script src="js/vendor/autoComplete.min.js"></script>


    <script>

        window.addEventListener('DOMContentLoaded', (event) => {
            window.maidea.init();

            new autoComplete({
                data: {
                    src: async () => {
                        const query = document.querySelector("#autoComplete").value;
                        const source = await fetch(`index.php?action=getCityAutocomplete&q=${query}`);
                        const data = await source.json();
                        return data;
                    },
                    key: ["title"],
                    cache: false
                },
                maxResults: 10,
                highlight: true,

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

