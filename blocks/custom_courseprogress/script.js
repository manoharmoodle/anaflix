var inactiveusers = parseInt($('#inactiveuser').val());
var activeusers = parseInt($('#activeuser').val());
var totalUsers = activeusers + inactiveusers;
var activeuserpercentage = (parseFloat((activeusers / totalUsers) * 100)).toFixed(2);
var inactiveuserpercentage = (parseFloat((inactiveusers / totalUsers) * 100)).toFixed(2);
var donutEl = document.getElementById("donut").getContext("2d");
const arr = [20,30,70,30]
// [activeuserpercentage, inactiveuserpercentage]
var myDonutChart = new Chart(donutEl, {
  type: "doughnut",
  data: {
    datasets: [
      {
        data:  [activeuserpercentage, inactiveuserpercentage] ,
        backgroundColor: ["#A154A1", "#6A68C5"],
        hoverBackgroundColor: ["#D74B8E", "#6A68C5"],
        labels: ["Red", "Green"], // Corrected labels to match the number of data values
      },
    ],
  },
  options: {
    tooltips: {
      callbacks: {
        label: function (tooltipItem, data) {
          var dataset = data.datasets[tooltipItem.datasetIndex];
          var value = dataset.data[tooltipItem.index];
          var percentage = ((value / dataset.data.reduce(function (a, b) { return a + b; })) * 100).toFixed(2);
          return data.labels[tooltipItem.index] + ': ' + value + ' (' + percentage + '%)'; // Concatenate '%' to the percentage
        },
      },
    },
    cutoutPercentage: 50,
    animation: {
      animateScale: true,
      animateRotate: true,
    },
    responsive: true,
    maintainAspectRatio: true,
    legend: {
      display: true,
      position: "right",
    },
  },
});

$("#select_month").change(function(){
  filtercohorts();
});
function userdownload(state) {
  var from = new Date($("#dateFrom").val());
  var to = new Date($("#dateTo").val());
  var url =
    M.cfg.wwwroot +
    "/blocks/custom_courseprogress/download.php?userstate="+state+"&from=" +
    $("#dateFrom").val() +
    "&to=" +
    $("#dateTo").val();
  var check = true;
  if (to == "Invalid Date" || from == "Invalid Date") {
    url =
      M.cfg.wwwroot +
      "/blocks/custom_courseprogress/download.php?userstate="+state+"&lastday=30";
    check = false;
  }
  if (from > to && check) {
    alert("From Date must be greater than To Date");
  } else {
    location.replace(url);
  }
}

function updateChartData(newData) {
  console.log(newData);
  myDonutChart.data.datasets[0].data = newData;
  myDonutChart.update();
}

async function filter_loginuser() {
  var from = new Date($("#dateFrom").val());
  var to = new Date($("#dateTo").val());
  var getdetail = true;

  if (to == "Invalid Date" || from == "Invalid Date") {
    getdetail = false;
    alert("Select a from and to date");
  } else if (from > to) {
    getdetail = false;
    alert("From Date must be greater than To Date");
  } else {
    $.ajax({
      url: M.cfg.wwwroot + "/blocks/custom_courseprogress/ajax.php",
      dataType: "json",
      data: { fromdate: $("#dateFrom").val(), todate: $("#dateTo").val() },
      success: function (returnData) {
        $('#loginActivity').hide();
        var totaluser = returnData.activeusers + returnData.inactiveusers;
        $('#activeUserCount').html('(' + returnData.activeusers + '/ ' + totaluser + ')');
        $('#inactiveUserCount').html('(' + returnData.inactiveusers + '/ ' + totaluser + ')');
        var totalUsers = returnData.activeusers + returnData.inactiveusers;
        var activeuserpercentage = (parseFloat((returnData.activeusers / totalUsers) * 100)).toFixed(2);
        var inactiveuserpercentage = (parseFloat((returnData.inactiveusers / totalUsers) * 100)).toFixed(2);
        const activeUser = document.querySelector('.active-user')
        const inActiveUser = document.querySelector('.inactive-user')
        const activeuservalue = Object.values(returnData.activeusersbusinesslines)
        const activeuserkey = Object.keys(returnData.activeusersbusinesslines)
        const inActiveuservalue = Object.values(returnData.inactiveusersbusinesslines)
        const inActiveuserkey = Object.keys(returnData.inactiveusersbusinesslines)
       
        let activeArrayOfObject = []
        for(let i = 0;i<activeuserkey.length;i++){
          activeArrayOfObject.push({name:activeuserkey[i],value:activeuservalue[i]})
        }
        let inActiveArrayOfObject = []
        for(let i = 0;i<inActiveuserkey.length;i++){
          inActiveArrayOfObject.push({name:inActiveuserkey[i],value:inActiveuservalue[i]})
        }
       const activeuser =  activeArrayOfObject.map((e)=>{
       return(`<li class="list-style" >${e.name} (${e.value}) / (${((e.value/(totalUsers))*100).toFixed(2)})%</li>`)
        }).join('')
       const inActiveuser =  inActiveArrayOfObject.map((e)=>{
       return(`<li class="list-style" >${e.name} (${e.value}) / (${((e.value/(totalUsers))*100).toFixed(2)})%</li>`)
        }).join('')
        activeUser.innerHTML = activeuser
        inActiveUser.innerHTML = inActiveuser

        updateChartData([
          `${activeuserpercentage}`,
          `${inactiveuserpercentage}`,
        ]);
      },
    });
  }
}

function filtercohorts() {
  let noofdays = $('#select_month').val();
  let val = $('#select1').val();
  let location = $('#location').val();
  $.ajax({
      type: "GET",
      url: M.cfg.wwwroot + "/blocks/custom_courseprogress/ajax.php", // PHP script that provides data
      dataType: "json",
      data: {courseid : val, noofdays : noofdays, location : location},
      success: function (returndata) {
        const data_key = Object.keys(returndata.response)
        const value_key = Object.values(returndata.response)
        updateline(data_key,value_key, returndata.coursename)
      }
  });
}

function course_access_report_download() {
  var courseid = $('#select1').val();
  let noofdays = $('#select_month').val();
  let businesslocation = $('#location').val();
  url = M.cfg.wwwroot + "/blocks/custom_courseprogress/download.php?courseid=" + courseid + "&lastday=" + noofdays + "&location=" + businesslocation;
  location.replace(url);
}

var config = {
  type: "line",
  data: {
      labels: ["Cohort-A", "Cohort-B", "Cohort-C", "Cohort-D", "Cohort-E", "Cohort-F", "Cohort-G"],
      datasets: [{
          label: "APAC PME",
          backgroundColor: "#904E8F",
          borderColor: "#904E8F",
          fill: false,
          lineTension: 0.3,
          data: [50, 300, 100, 450, 150, 200, 300],
      }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: false,
        suggestedMin: 1,
        ticks: {
          precision: 0,
        },
      }
    }
  }
};

window.onload = function () {
  var ctx = document.getElementById("canvas").getContext("2d");
  let val = $('#select1').val();
  let noofdays = $('#select_month').val();
  $.ajax({
    type: "GET",
    url: M.cfg.wwwroot + "/blocks/custom_courseprogress/ajax.php", // PHP script that provides data
    dataType: "json",
    data: {courseid : val, noofdays : noofdays},
    success: function (returndata) {
      const data_label = Object.keys(returndata.response)
      const value_set = Object.values(returndata.response)
      
      // jazib 
      config.data.labels = data_label
      config.data.datasets[0].data = value_set
      config.data.datasets[0].label = returndata.coursename
      window.myLine = new Chart(ctx, config);
    }
  });
};

// jazib
function updateline(data_key,value_key,label){
  config.data.labels = data_key
  config.data.datasets[0].data = value_key
  config.data.datasets[0].label = label
  myLine.update()
}