# Storyblok Lens - Check Space {{ spaceId }}

## Checking Space ({{ spaceId }}): {{ space.get("name") }}

- Space: {{ space['name'] }}
- Region: {{ space['region'] }}
- Traffic usage (this month): {{ traffic['traffic_used_this_month']|to_bytes }}
- Traffic usage (last 5 days): {{ traffic['total_traffic_per_time_period']|to_bytes }}
- API Requests (last 5 days): {{ traffic['total_requests_per_time_period'] }}
- API Server Location: {{ space['region'] }}
- Stories: {{ space['stories_count'] }}
- Assets: {{ space['assets_count'] }}
- Blocks/Components: {{ components.count() }}
- Users: {{ statistics.get("collaborators_count", "N/A") }}
- Max Users: {{ statistics.get("collaborators_limit", "N/A") }}

### Space Limits

- Plan Level: {{ space.get('plan_level') }} - {{ space.get('plan_level')|plan_description }}
- Traffic Limit: {{ space.get('traffic_limit')|to_bytes }}
- Activities Owner Filter: {{ space.get('limits.activities_owner_filter') }}
- Activities Type Filter: {{ space.get('limits.activities_type_filter') }}
- Activities Past Days Filter: {{ space.get('limits.activities_past_days_filter') }}
- Max Custom Workflows: {{ space.get('limits.max_custom_workflows') }}
- Max Workflow Stages: {{ space.get('limits.max_workflow_stages') }}
- Min Character Search: {{ space.get('limits.min_character_search') }}

### Environments

| Name | Location |
| ----- | -------- |
{% for environment in space["environments"] %}
| {{ environment["name"] }} | {{ environment["location"] }} |
{% endfor %}

### Languages

{% if (space.getBlock("options.languages").count() > 0 ) %}
Here the list of defined languages ({{ space.getBlock("options.languages").count() }})
| Lang Code | Language |
| ----- | -------- |
{% for lang in space.getBlock("options.languages") %}
| {{ lang.get("code") }} | {{ lang.get("name") }} |
{% endfor %}
{% else %}

The space has no defined custom Languages.
For defining new Languages in the space settings:
https://app.storyblok.com/#/me/spaces/{{ spaceId }}/settings?tab=internationalization

Documentation about Internazionalization:
https://www.storyblok.com/docs/guide/in-depth/internationalization

{% endif %}


### User Roles

{% if (space.getBlock("space_roles").count() > 0 ) %}
| Role | ID Role |
| ----- | -------- |
{% for role in space.get("space_roles") %}
| {{ role["role"] }} | {{ role["id"] }} |
{% endfor %}
{% else %}

The space has no defined custom User Roles.
For defining new Roles in the space settings:
https://app.storyblok.com/#/me/spaces/{{ spaceId }}/settings?tab=roles

Documentation about Roles and Permissions:
https://www.storyblok.com/docs/guide/in-depth/roles-and-permissions

{% endif %}

### Installed Applications

{% if (apps.count() > 0 ) %}
These are the applications ({{ apps.count() }}) installed with the Space:

| Name | Slug | Description |
| ---- | ---- | ----------- |
{% for app in apps %}
| {{ app["name"] }} | {{ app["slug"] }} | {{ app["intro"] }} |
{% endfor %}
{% else %}

The space has no installed applications.
For installing new applications you can explore:
https://app.storyblok.com/#/me/spaces/{{ spaceId }}/apps
{% endif %}
