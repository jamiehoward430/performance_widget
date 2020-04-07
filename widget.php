<script src="<?=$us_url_root?>usersc/widgets/logins/Chart.bundle.js"></script>
  <div class="row">
<!-- This is an example widget file.  It will be included on the statistics page of the Dashboard. -->
<!-- Do any php that needs to happen. You already have access to the db -->
<?php

$db = DB2::getInstance();

$date = date("Y-m-d H:i:s");
$load_times = '0,';
$load_queries = '0,';

if(!function_exists('checkAvgVal')) {
   function checkAvgVal($val){
     if(empty($val->avg)){
      return '0, ';
     }else{
      return $val->avg.', ';
     }
    }
 }

$b = $db->query("SELECT Avg(ms) as avg FROM plg_perf_log WHERE logged < ? AND logged > ?",array(date("Y-m-d H:i:s", strtotime("-52 weeks", strtotime($date))),date("Y-m-d H:i:s", strtotime("-53 weeks", strtotime($date)))))->first();
$load_times.=checkAvgVal($b);
$b = $db->query("SELECT Avg(ms) as avg FROM plg_perf_log WHERE logged < ? AND logged > ?",array(date("Y-m-d H:i:s", strtotime("-2 weeks", strtotime($date))),date("Y-m-d H:i:s", strtotime("-3 weeks", strtotime($date)))))->first();
$load_times.=checkAvgVal($b);
$b = $db->query("SELECT Avg(ms) as avg FROM plg_perf_log WHERE logged < ? AND logged > ?",array(date("Y-m-d H:i:s", strtotime("-1 weeks", strtotime($date))),date("Y-m-d H:i:s", strtotime("-2 weeks", strtotime($date)))))->first();
$load_times.=checkAvgVal($b);
//This week
$b = $db->query("SELECT Avg(ms) as avg FROM plg_perf_log WHERE logged > ?",array(date("Y-m-d H:i:s", strtotime("-1 week", strtotime($date)))))->first();
$load_times.=checkAvgVal($b);

$b = $db->query("SELECT Avg(queries) as avg FROM plg_perf_log WHERE logged < ? AND logged > ?",array(date("Y-m-d H:i:s", strtotime("-52 weeks", strtotime($date))),date("Y-m-d H:i:s", strtotime("-53 weeks", strtotime($date)))))->first();
$load_queries.=checkAvgVal($b);
$b = $db->query("SELECT Avg(queries) as avg FROM plg_perf_log WHERE logged < ? AND logged > ?",array(date("Y-m-d H:i:s", strtotime("-2 weeks", strtotime($date))),date("Y-m-d H:i:s", strtotime("-3 weeks", strtotime($date)))))->first();
$load_queries.=checkAvgVal($b);
$b = $db->query("SELECT Avg(queries) as avg FROM plg_perf_log WHERE logged < ? AND logged > ?",array(date("Y-m-d H:i:s", strtotime("-1 weeks", strtotime($date))),date("Y-m-d H:i:s", strtotime("-2 weeks", strtotime($date)))))->first();
$load_queries.=checkAvgVal($b);
//This week
$b = $db->query("SELECT Avg(queries) as avg FROM plg_perf_log WHERE logged > ?",array(date("Y-m-d H:i:s", strtotime("-1 week", strtotime($date)))))->first();
$load_queries.=checkAvgVal($b);

?>



<?php
//php for charts

$p = $db->query("SELECT page, AVG(ms) FROM plg_perf_log GROUP BY page ORDER BY AVG(ms) DESC")->results();
$l = $db->query("SELECT page, AVG(queries) FROM plg_perf_log GROUP BY page ORDER BY AVG(queries) DESC")->results();

?>

<!-- Create a div to hold your widget -->
<div class="col-lg-6">
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3">Average page load time per page</h4>
      <?php createChart($p,['title'=>'','type'=>'bar']); ?>
    </div>
  </div>
</div><!-- /# column -->

<div class="col-lg-6">
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3">Average DB queries per page</h4>
      <?php createChart($l,['title'=>'','type'=>'bar']); ?>
    </div>
  </div>
</div><!-- /# column -->

<div class="col-lg-6">
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3">Average page load time</h4>
      <canvas id="perfB"></canvas>
    </div>
  </div>
</div><!-- /# column -->
<!-- Create a div to hold your widget -->



  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
      <h4 class="mb-3">Average DB queries</h4>
        <canvas id="perfC"></canvas>
      </div>
    </div>
  </div><!-- /# column -->


</div> <!-- end widget section -->
<!-- Put any javascript here -->
<script type="text/javascript">
$(document).ready(function() {


var ctx = document.getElementById( "perfB" );
ctx.height = 150;
var myChart = new Chart( ctx, {
  type: 'line',
  data: {
    labels: [" ","This week Last Year", "2 Weeks Ago", "1 Week Ago", "Last 7 days" ],
    type: 'line',
    defaultFontFamily: 'Montserrat',
    datasets: [ {
      label: "ms",
      data: [ <?=$load_times?> ],
      backgroundColor: 'transparent',
      borderColor: 'rgba(40,167,69,0.75)',
      borderWidth: 3,
      pointStyle: 'circle',
      pointRadius: 5,
      pointBorderColor: 'transparent',
      pointBackgroundColor: 'rgba(40,167,69,0.75)',
    }]
  },
  options: {
    responsive: true,

    tooltips: {
      mode: 'index',
      titleFontSize: 12,
      titleFontColor: '#000',
      bodyFontColor: '#000',
      backgroundColor: '#fff',
      titleFontFamily: 'Montserrat',
      bodyFontFamily: 'Montserrat',
      cornerRadius: 3,
      intersect: false,
    },
    legend: {
      display: false,
      labels: {
        usePointStyle: true,
        fontFamily: 'Montserrat',
      },
    },
    scales: {
      xAxes: [ {
        display: true,
        gridLines: {
          display: false,
          drawBorder: false
        },
        scaleLabel: {
          display: false,
          labelString: 'Time'
        }
      } ],
      yAxes: [ {
        display: true,
        gridLines: {
          display: false,
          drawBorder: false
        },
        scaleLabel: {
          display: true,
          labelString: 'Load time in ms'
        }
      } ]
    },
    title: {
      display: false,
      text: 'Normal Legend'
    }
  }
} );

var ctx = document.getElementById( "perfC" );
ctx.height = 150;
var myChart = new Chart( ctx, {
  type: 'line',
  data: {
    labels: [" ","This week Last Year", "2 Weeks Ago", "1 Week Ago", "Last 7 days" ],
    type: 'line',
    defaultFontFamily: 'Montserrat',
    datasets: [ {
      label: "Queries",
      data: [ <?=$load_queries?> ],
      backgroundColor: 'transparent',
      borderColor: 'rgba(40,167,69,0.75)',
      borderWidth: 3,
      pointStyle: 'circle',
      pointRadius: 5,
      pointBorderColor: 'transparent',
      pointBackgroundColor: 'rgba(40,167,69,0.75)',
    }]
  },
  options: {
    responsive: true,

    tooltips: {
      mode: 'index',
      titleFontSize: 12,
      titleFontColor: '#000',
      bodyFontColor: '#000',
      backgroundColor: '#fff',
      titleFontFamily: 'Montserrat',
      bodyFontFamily: 'Montserrat',
      cornerRadius: 3,
      intersect: false,
    },
    legend: {
      display: false,
      labels: {
        usePointStyle: true,
        fontFamily: 'Montserrat',
      },
    },
    scales: {
      xAxes: [ {
        display: true,
        gridLines: {
          display: false,
          drawBorder: false
        },
        scaleLabel: {
          display: false,
          labelString: 'Time'
        }
      } ],
      yAxes: [ {
        display: true,
        gridLines: {
          display: false,
          drawBorder: false
        },
        scaleLabel: {
          display: true,
          labelString: 'DB Queries'
        }
      } ]
    },
    title: {
      display: false,
      text: 'Normal Legend'
    }
  }
} );

}); //End DocReady
</script>
