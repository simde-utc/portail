<canvas id="myChart" width="200" height="200"></canvas>
<script>
$(function () {
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($data)) ?>,
            datasets: [{
                label: 'Nombre d\'utilisateurs cumulé',
                data: <?php echo json_encode(array_values($data)) ?>,
                backgroundColor: '#35F',
                borderColor: '#35F',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
});
</script>
