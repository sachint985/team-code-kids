$('#graphs').ready(function(){
var selectVal = $('.select select').val();
$('.select select').on('change',function(){
   selectVal = $('.select select').val();
   loadChart();
})
// grievance_type
function loadChart() {
 
$.getJSON('http://127.0.0.1:8000/principal/chart/status/'+selectVal, function(data) {
Highcharts.chart('grievance_type_committee', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 0
        }
    },
    title: {
        text: 'Types of Grievance Committee Wise'
    },
    plotOptions: {
         series: {
            dataLabels: {
                enabled: true,
                formatter: function() {
                    return Math.round(this.percentage*100)/100 + ' %';
                },
                distance: 15,
                
            }
        },
        pie: {
            innerSize:70,
            depth: 50,
            dataLabels: {
                enabled: false,
                format: '{point.percentage:.1f}%'
                
            },
            showInLegend:true,
        },
        
        
    },
    legend: {
        align: 'right',
        layout: 'vertical',
        verticalAlign: 'top',
        x: -20,
        y: 60,
        backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
        borderColor: '#CCC',
        borderWidth: 1,
        shadow: false,

    },
    credits: {
        enabled: false
    },
    series: [{
        name: 'Total',
        'data': data[0]
    }]
});
});

}

loadChart();

// grievance_yearwise
$.getJSON('http://127.0.0.1:8000/grievance/aicte/chart/year', function(data) {
Highcharts.chart('top5_committeeWise', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Yearwise filled Grievances'
    },
    xAxis: {
        categories: data.year
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Grievance Count'
        },
        stackLabels: {
            enabled: true,
            style: {
                fontWeight: 'bold',
                color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
            }
        }
    },
    
    tooltip: {
        headerFormat: '<b>{point.x}</b><br/>',
        pointFormat: '{series.name}: {point.y}'
    },
    plotOptions: {
        column: {
            stacking: 'normal',
            dataLabels: {
                enabled: false,
                color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
            }
        }
    },
    credits: {
        enabled: false
    },
    series: [{
        name: 'Filled Grievance',
        data: data.count
    }]
});
});

// top5_state
$.getJSON('http://127.0.0.1:8000/principal/chart/type', function(data) {
Highcharts.chart('grievance_type', {
   chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 0
        }
    },
    title: {
        text: 'Types of Grievance Committee Wise'
    },
    plotOptions: {
         series: {
            dataLabels: {
                enabled: true,
                formatter: function() {
                    return Math.round(this.percentage*100)/100 + ' %';
                },
                distance: 15,
                
            }
        },
        pie: {
            innerSize:70,
            depth: 50,
            dataLabels: {
                enabled: false,
                format: '{point.percentage:.1f}%'
                
            },
            showInLegend:true,
        },
        
        
    },
    legend: {
        align: 'right',
        layout: 'vertical',
        verticalAlign: 'top',
        x: -20,
        y: 60,
        backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
        borderColor: '#CCC',
        borderWidth: 1,
        shadow: false,

    },
    credits: {
        enabled: false
    },
    series: [{
        name: 'Total',
        'data': data
    }]
});
});
// api end
});
// jquery end