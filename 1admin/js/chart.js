      // ✅ Bar Chart for Deployments
      const ctx1 = document.getElementById('chart1').getContext('2d');
      new Chart(ctx1, {
          type: 'bar',
          data: {
              labels: ['Jan', 'Feb', 'Mar', 'Apr'],
              datasets: [{
                  label: 'Deployments',
                  data: [10, 20, 30, 40],
                  backgroundColor: 'rgba(75, 192, 192, 0.2)',
                  borderColor: 'rgba(75, 192, 192, 1)',
                  borderWidth: 1
              }]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false
          }
      });

      // ✅ Line Chart for Progress
      const ctx2 = document.getElementById('chart2').getContext('2d');
      new Chart(ctx2, {
          type: 'line',
          data: {
              labels: ['Week 1', 'Week 2', 'Week 3'],
              datasets: [{
                  label: 'Progress',
                  data: [50, 70, 90],
                  borderColor: 'blue',
                  borderWidth: 2
              }]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false
          }
      });