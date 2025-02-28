## Statistics overview

Counting usage:
- Collaborators: {{ statistics.get("collaborators_count") }}
- Stories: {{ statistics.get("all_stories_count") }}
- Folders: {{ statistics.get("all_folders_count") }}
- Roles: {{ statistics.get("all_space_roles_count") }}
- Assets: {{ statistics.get("all_assets_count") }}
- Components: {{ statistics.get("all_components_count") }}
- Datasources: {{ statistics.get("all_datasources_count") }}
- Image invalidation: {{ statistics.get("image_invalidation_count") }}
- Preview URLs: {{ statistics.get("preview_urls_count") }}
- Webhook: {{ statistics.get("all_webhooks_count") }}
- Workflow: {{ statistics.get("all_workflows_count") }}
- Workflow stages: {{ statistics.get("all_workflow_stages_count") }}
- Scheduled single stories: {{ statistics.get("all_schedule_single_story_count") }}
- Custom meta Assets: {{ statistics.get("all_asset_custom_meta_data_count") }}
- Releases: {{ statistics.get("all_releases_count") }}
- Pipelines: {{ statistics.get("all_pipelines_count") }}

Retention days (-1 is unlimited):
- Webhook logs: {{ statistics.get("webhook_log_retention_in_days") }}
- Stories: {{ statistics.get("story_history_retention_in_days") }}
- Activities: {{ statistics.get("activity_log_period_in_days") }}

Limits:
- Collaborators: {{ statistics.get("collaborators_limit") }}
- Image invalidation limit: {{ statistics.get("image_invalidation_limit") }}
- Asset custom upload: {{ statistics.get("asset_custom_upload_limit") }}



## Updating activities per day

| Day | Activities |
| --- | -------- |
{% for statistic in statistics.getBlock("updated_activities").orderBy("day", "desc") %}
| {{ statistic.getFormattedDateTime("day", "dS F Y") }} | {{ statistic.get("counting") }} |
{% endfor %}

## Traffic usage per month

| Month | Requests | Traffic |
| ----- | -------- | ------- |
{% for statistic in statistics.getBlock("monthly_traffic").orderBy("month_col", "desc") %}
| {{ statistic["month_col"] }} | {{ statistic["counting"] }} reqs. | {{ statistic["total_bytes"]|to_bytes }} |
{% endfor %}

## API logs, daily usage current month

| Month | Requests | Traffic |
| ----- | -------- | ------- |
{% for statistic in statistics.getBlock("api_logs_per_month").orderBy("created_at", "desc") %}
| {{
    statistic.getFormattedDateTime("created_at", "dS F Y")
}} | {{
    statistic.get("counting")
}} reqs. | {{
    statistic.getFormattedByte("total_bytes")
}} |
{% endfor %}

## API logs

| Month | Requests | Traffic |
| ----- | -------- | ------- |
{% for statistic in statistics.getBlock("api_logs").orderBy("day", "desc") %}
| {{ statistic.getFormattedDateTime("day", "dS F Y") }} | {{ statistic.get("counting") }} reqs. | {{ statistic.getFormattedByte("total_bytes") }} |
{% endfor %}

## Traffic overview

- Monthly traffic limit: {{ traffic.getFormattedByte("montly_traffic_limit") }}
- Traffic limit: {{ traffic.getFormattedByte("traffic_limit") }}
- Traffic used this month: {{ traffic.getFormattedByte("traffic_used_this_month") }}
- Requests used last days: {{ traffic.get("requests_used_last_days") }}
- Total traffic per time period: {{ traffic.getFormattedByte("total_traffic_per_time_period") }}
- Total requests per time period: {{ traffic.get("total_requests_per_time_period") }}

## Traffic usage (daily, last days)

| Day | Requests | Traffic |
| --- | -------- | ------- |
{% for traffic in traffic.getBlock("traffic").orderBy("date", "desc") %}
| {{ traffic.getFormattedDateTime("date", "dS F Y") }} | {{ traffic.get("api_requests") }} reqs. | {{ traffic.getFormattedByte("total_bytes") }} |
{% endfor %}
