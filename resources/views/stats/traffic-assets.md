## Traffic Assets usage


| File | Total Bytes | Content Type | Content Size |
| ----- | -------- | -------- | -------- |
{% for asset in assetsTraffic %}
| {{ asset.get("filename") }} | {{ asset.getFormattedByte("total_bytes") }} | {{ asset.get("content_type") }} | {{ asset.getFormattedByte("content_length") }} |
{% endfor %}
