google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes);

function stringToDate(arr){
    arr.forEach(function(item, index, array) {
        item[0] = new Date(item[0]);
    })
}

function drawCurveTypes() {




    stringToDate(b);
    stringToDate(c);

    var data = new google.visualization.DataTable();
    data.addColumn('datetime', 'Date');
    data.addColumn('number', 'Stock low');
    data.addRows(b);


    var data2 = new google.visualization.DataTable();
    data2.addColumn('datetime', 'Date');
    data2.addColumn('number', 'Stock low');
    data2.addRows(c);




    var options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Popularity'
        },
        series: {
            1: {curveType: 'function'}
        }
    };

    var options2 = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'test'
        },
        series: {
            1: {curveType: 'function'}
        }
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    var chart2 = new google.visualization.LineChart(document.getElementById('chart_div2'));
    chart.draw(data, options);
    chart2.draw(data2, options2);
}