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
    airHumData.addColumn('number', 'air humidity \n(%)');
    airHumData.addRows(airHumidityData);

    var airTempData = new google.visualization.DataTable();
    airTempData.addColumn('datetime', 'Date');
    airTempData.addColumn('number', 'temperature \n(°C)');
    airTempData.addRows(airTemperatureData);

    var waterLvlData = new google.visualization.DataTable();
    waterLvlData.addColumn('datetime', 'Date');
    waterLvlData.addColumn('number', 'water level \n(%)');
    waterLvlData.addRows(waterLevelData);

    var lightLvlData = new google.visualization.DataTable();
    lightLvlData.addColumn('datetime', 'Date');
    lightLvlData.addColumn('number', 'light \n(lux)');
    lightLvlData.addRows(lightLevelData);

    var soilMoistData = new google.visualization.DataTable();
    soilMoistData.addColumn('datetime', 'Date');
    soilMoistData.addColumn('number', 'soil moisture \n(%)');
    soilMoistData.addRows(soilMoistureData);




    var AirTempOptions = {
        hAxis: {
            title: 'time'
        },
        vAxis: {
            title: 'air temperature',
            ticks: [15, 20, 25, 30, 35,]
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
            title: 'air humidity',
            ticks: [20, 40, 60, 80, 100]
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
            title: 'water level',
            ticks: [0, 25, 50, 75, 100]
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
            title: 'light',
            scaleType: 'log',
            ticks: [0, 1000, 10000, 100000]
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
            title: 'soil moisture',
            ticks: [0, 25, 50, 75, 100]
        },
        series: {
            1: {curveType: 'function'}
        }
    };


    var AirTempChart = new google.visualization.LineChart(document.getElementById('ChartAirTemp'));
    var AirHumChart = new google.visualization.LineChart(document.getElementById('ChartAirHum'));
    var waterLvlChart = new google.visualization.LineChart(document.getElementById('ChartwaterLvl'));
    var lightLvlChart = new google.visualization.LineChart(document.getElementById('ChartlightLvl'));
    var soilMoistChart = new google.visualization.LineChart(document.getElementById('ChartsoilMoist'));

    AirTempChart.draw(airTempData, AirTempOptions);
    AirHumChart.draw(airHumData, AirHumOptions);
    waterLvlChart.draw(waterLvlData, waterLvlOptions);
    lightLvlChart.draw(lightLvlData, lightLvlOptions);
    soilMoistChart.draw(soilMoistData, soilMoistOptions);
}