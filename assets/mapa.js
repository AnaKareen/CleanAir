$(document).ready(function () {
    var data = Highcharts.geojson(Highcharts.maps["countries/mx/mx-all"]),
        separators = Highcharts.geojson(
            Highcharts.maps["countries/mx/mx-all"],
            "mapline"
        ),
        small = $("#container").width() < 400;

    // Set drilldown pointers
    $.each(data, function (i) {
        this.drilldown = this.properties["hc-key"];
        this.value = i;
    });

    // Instantiate the map
    Highcharts.mapChart("container", {
        chart: {
            events: {
                drilldown: function (e) {
                    if (!e.seriesOptions) {
                        var chart = this,
                            mapKey = "countries/mx/" + e.point.drilldown + "-all",
                            fail = setTimeout(function () {
                                if (!Highcharts.maps[mapKey]) {
                                    chart.showLoading(
                                        '<i class="icon-frown"></i> Failed loading ' + e.point.name
                                    );
                                    fail = setTimeout(function () {
                                        chart.hideLoading();
                                    }, 1000);
                                }
                            }, 3000);

                        chart.showLoading('<i class="icon-spinner icon-spin icon-3x"></i>');

                        $.getScript(
                            "https://code.highcharts.com/mapdata/" + mapKey + ".js",
                            function () {
                                data = Highcharts.geojson(Highcharts.maps[mapKey]);
                                $.each(data, function (i) {
                                    this.value = i;
                                });
                                chart.hideLoading();
                                clearTimeout(fail);
                                chart.addSeriesAsDrilldown(e.point, {
                                    name: e.point.name,
                                    data: data,
                                    dataLabels: {
                                        enabled: true,
                                        format: "{point.name}"
                                    }
                                });
                            }
                        );
                    }

                    this.setTitle(null, { text: e.point.name });
                },
                drillup: function () {
                    this.setTitle(null, { text: "" });
                }
            }
        },
        title: { text: "Mapa de México" },
        subtitle: {
            text: "",
            floating: true,
            align: "right",
            y: 50,
            style: { fontSize: "16px" }
        },
        legend: small ? {} : { layout: "vertical", align: "right", verticalAlign: "middle" },
        colorAxis: { min: 0, minColor: "#E6E7E8", maxColor: "#005645" },
        mapNavigation: { enabled: true, buttonOptions: { verticalAlign: "bottom" } },
        plotOptions: { map: { states: { hover: { color: "#EEDD66" } } } },
        series: [
            { data: data, name: "", dataLabels: { enabled: true, format: "{point.properties.postal-code}" } },
            { type: "mapline", data: separators, color: "silver", enableMouseTracking: false, animation: { duration: 500 } }
        ],
        drilldown: {
            activeDataLabelStyle: { color: "#FFFFFF", textDecoration: "none", textOutline: "1px #000000" },
            drillUpButton: { relativeTo: "spacingBox", position: { x: 0, y: 60 } }
        }
    });
});
