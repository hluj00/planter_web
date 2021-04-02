google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawCurveTypes);

function stringToDate(arr){
    arr.forEach(function(item, index, array) {
        item[0] = new Date(item[0]);
    })
}

function drawCurveTypes() {




    stringToDate(airHumidityData);
    stringToDate(airTemperatureData);
    stringToDate(waterLevelData);
    stringToDate(lightLevelData);
    stringToDate(soilMoistureData);

    var airHumData = new google.visualization.DataTable();
    airHumData.addColumn('datetime', 'Date');
    airHumData.addColumn('number', 'Stock low');
    airHumData.addRows(airHumidityData);

    var airTempData = new google.visualization.DataTable();
    airTempData.addColumn('datetime', 'Date');
    airTempData.addColumn('number', 'Stock low');
    airTempData.addRows(airTemperatureData);

    var waterLvlData = new google.visualization.DataTable();
    waterLvlData.addColumn('datetime', 'Date');
    waterLvlData.addColumn('number', 'Stock low');
    waterLvlData.addRows(waterLevelData);

    var lightLvlData = new google.visualization.DataTable();
    lightLvlData.addColumn('datetime', 'Date');
    lightLvlData.addColumn('number', 'Stock low');
    lightLvlData.addRows(lightLevelData);

    var soilMoistData = new google.visualization.DataTable();
    soilMoistData.addColumn('datetime', 'Date');
    soilMoistData.addColumn('number', 'Stock low');
    soilMoistData.addRows(soilMoistureData);




    var AirTempOptions = {
        hAxis: {
            title: 'time'
        },
        vAxis: {
            title: 'air temperature'
        },
        series: {
            1: {curveType: 'function'}
        }
    };

    var AirHumOptions = {
        hAxis: {
            title: 'time'
        },
        vAxis: {
            title: 'air humidity'
        },
        series: {
            1: {curveType: 'function'}
        }
    };

    var waterLvlOptions = {
        hAxis: {
            title: 'time'
        },
        vAxis: {
            title: 'water level'
        },
        series: {
            1: {curveType: 'function'}
        }
    };

    var lightLvlOptions = {
        hAxis: {
            title: 'time'
        },
        vAxis: {
            title: 'light'
        },
        series: {
            1: {curveType: 'function'}
        }
    };

    var soilMoistOptions = {
        hAxis: {
            title: 'time'
        },
        vAxis: {
            title: 'soil moisture'
        },
        series: {
            1: {curveType: 'function'}
        }
    };


    var AirTempChart = new google.visualization.LineChart(document.getElementById('chart_div'));
    var AirHumChart = new google.visualization.LineChart(document.getElementById('chart_div2'));
    var waterLvlChart = new google.visualization.LineChart(document.getElementById('chart_div3'));
    var lightLvlChart = new google.visualization.LineChart(document.getElementById('chart_div4'));
    var soilMoistChart = new google.visualization.LineChart(document.getElementById('chart_div5'));

    AirTempChart.draw(airHumData, AirTempOptions);
    AirHumChart.draw(airTempData, AirHumOptions);
    waterLvlChart.draw(waterLvlData, waterLvlOptions);
    lightLvlChart.draw(lightLvlData, lightLvlOptions);
    soilMoistChart.draw(soilMoistData, soilMoistOptions);
}