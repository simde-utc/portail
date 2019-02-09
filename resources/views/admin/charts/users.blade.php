<canvas id="users"></canvas>
<canvas id="users-bar"></canvas>
<canvas id="auths"></canvas>
<script>
$(function () {
    new Chart(document.getElementById("users").getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($users)) !!},
            datasets: [{
                label: 'Utilisateurs cumulés',
                data: {!! json_encode(array_values($users)) !!},
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

    new Chart(document.getElementById("users-bar").getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($usersBar)) !!},
            datasets: [{
                label: 'Nouveaux utilisateurs par jour',
                data: {!! json_encode(array_values($usersBar)) !!},
                borderColor: '#35F',
                borderWidth: 1
            }]
        },
    });

    new Chart(document.getElementById("auths").getContext('2d'), {
        type: 'radar',
        data: {
            labels: {!! json_encode(array_keys($auths)) !!},
            datasets: [{
                label: 'Nombre d\'utilisateurs cumulé',
                data: {!! json_encode(array_values($auths)) !!},
                borderColor: '#35F',
                borderWidth: 1
            }]
        }
    });
});
</script>
