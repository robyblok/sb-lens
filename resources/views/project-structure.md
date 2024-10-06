# Project Structure

### Applications for managing the Space's Structure

Storyblok provides some application for managing the structure of the Space.

#### The Dimension Application

[Dimension Application](https://www.storyblok.com/apps/locales) easily manages multi-tree structures for languages and more.

{% if hasDimensionApp %}
Dimension Application is installed.
{% else %}
Dimension Application is not installed.
This app is helpful when you choose to have a multi-tree structure to manage different versions of your content. Please read [the guide about internationalization](https://www.storyblok.com/docs/internationalization) to see which options are available using Storyblok.
{% endif %}

#### The Pipeline Application

The [Pipeline Application](https://www.storyblok.com/apps/branches) allows the setup of a content staging workflow with pipelines (i.e. development, staging, production).

{% if hasPipelineApp %}
Pipeline Application is installed.
The configured Pipeline stages are:

{% for branch in branches["branches"] %}
- {{ branch['name'] }}
{% endfor %}

{% else %}
Pipeline Application is not installed.
This app helps manage multiple "content stages".
The features of Pipeline Application:
- Multiple content pipeline stages
- One click deployment from one pipeline stage to another
- Preview functionality
- Access tokens for each branch
{% endif %}


### First level folders

The first-level folders in Storyblok are crucial for understanding and organizing content effectively within a space. They allow you to structure content by regions, such as EMEA, AMER, or APAC, or by countries like Italy, Spain, and France, providing clear segmentation. Alternatively, these folders can be used to categorize content by brands or projects, ensuring that different aspects of the content strategy are easily accessible and well-organized. This hierarchical structure at the first level helps in maintaining clarity and efficiency in content management.

First level folders:
{% for folder in folders["stories"] %}
- {{ folder['name'] }} ({{ folder['slug'] }})
{% endfor %}
