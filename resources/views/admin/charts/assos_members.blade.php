<canvas id="assos-members"></canvas>
<canvas id="assos-members-day"></canvas>
<canvas id="assos-members-month"></canvas>
<canvas id="assos-members-semester"></canvas>
<canvas id="assos-per-member"></canvas>
<script>
$(function () {
    new Chart(document.getElementById("assos-members").getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($members)) !!},
            datasets: [{
                label: 'Membres cumulés',
                data: {!! json_encode(array_values($members)) !!},
                borderColor: '#35F',
                borderWidth: 1
            }, {
                label: 'Membres uniques cumulés',
                data: {!! json_encode(array_values($uMembers)) !!},
                borderColor: '#F53',
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

    new Chart(document.getElementById("assos-members-day").getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($membersByDay)) !!},
            datasets: [{
                label: 'Nouveaux membres par jour',
                data: {!! json_encode(array_values($membersByDay)) !!},
                borderColor: '#35F',
                borderWidth: 1
            }, {
                label: 'Nouveaux membres uniques par jour',
                data: {!! json_encode(array_values($uMembersByDay)) !!},
                borderColor: '#F53',
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

    new Chart(document.getElementById("assos-members-month").getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($membersByMonth)) !!},
            datasets: [{
                label: 'Nouveaux membres par mois',
                data: {!! json_encode(array_values($membersByMonth)) !!},
                borderColor: '#35F',
                borderWidth: 1
            }, {
                label: 'Nouveaux membres uniques par mois',
                data: {!! json_encode(array_values($uMembersByMonth)) !!},
                borderColor: '#F53',
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

    new Chart(document.getElementById("assos-members-semester").getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($membersBySemester)) !!},
            datasets: [{
                label: 'Nombre de membres cumulé par semestre',
                data: {!! json_encode(array_values($membersBySemester)) !!},
                borderColor: '#35F',
                borderWidth: 1
            }, {
                label: 'Nombre de membres cumulé par semestre',
                type: 'line',
                data: {!! json_encode(array_values($membersBySemester)) !!},
                borderColor: '#35F',
                borderWidth: 1
            }, {
                label: 'Nombre de membres uniques cumulé par semestre',
                data: {!! json_encode(array_values($uMembersBySemester)) !!},
                borderColor: '#F53',
                borderWidth: 1
            }, {
                label: 'Nombre de membres uniques cumulé par semestre',
                type: 'line',
                data: {!! json_encode(array_values($uMembersBySemester)) !!},
                borderColor: '#F53',
                borderWidth: 1
            }]
        }
    });

    new Chart(document.getElementById("assos-per-member").getContext('2d'), {
        type: 'radar',
        data: {
            labels: {!! json_encode(array_keys($assosPerMemberKeys)) !!},
            datasets: [
            @foreach ($assosPerMemberValues as $label => $data)
                {
                    label: '{{ $label }}',
                    data: {!! json_encode(array_values($data)) !!},
                    borderWidth: 1
                },
            @endforeach
            ]
        }
    });
});
</script>
