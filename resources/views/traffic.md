## Traffic usage (monthly)

| Month | Requests | Traffic |
| ----- | -------- | ------- |
{% for statistic in statistics["monthly_traffic"] %}
| {{ statistic["month_col"] }} | {{ statistic["counting"] }} reqs. | {{ statistic["total_bytes"]|to_bytes }} |
{% endfor %}
